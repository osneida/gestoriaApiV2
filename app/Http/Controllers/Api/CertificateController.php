<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificatePeriod;
use App\Models\Company;
use App\Models\Setting;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Str;

use Smalot\PdfParser\Parser;

class CertificateController extends Controller
{


    const NIF_REGEX = '/NIF (\w+)/m';
    const ID_REGEX = '/[0-9A-Z][0-9]{7,7}[A-Za-z]/';

    protected $processedEachWorkerSuccessfully;
    protected $processedFullCertificateForAgency;
    protected $currentPathForCertificate;
    protected $arrayErrorsOnCheckCertificateFile;
    protected $currentWorkerDNI;
    protected $currentWorkerFile;
    protected $success = true;
    protected $errorDNI = [];


    public function construct()
    {
        $this->processedEachWorkerSuccessfully = false;
        $this->processedFullCertificateForAgency = false;
        $this->arrayErrorsOnCheckCertificateFile = [];
        $this->errorDNI = [];
    }

    public function index()
    {
        $itemsPerPage = (int) request('itemsPerPage');
        $payrolls = Certificate::filtered();
        return response()->json(["success" => true, "data" => $payrolls->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10)]);
    }

    public function allFiltered()
    {

        $payrolls = Certificate::filtered();
        return response()->json(["success" => true, "data" => $payrolls->get()]);
    }

    public function generateS3SignedUrl()
    {

        try {
            $url = Storage::disk('s3')->temporaryUrl(
                request('document_file'),
                Carbon::now()->addMinutes(Certificate::TIME_SIGNATURE_S3_TEMPORARY_URL)
            );
            return response()->json(["success" => true, "signed_url" => $url]);
        } catch (\Exception $exception) {
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }

    private function setMessages($period)
    {

        $subject = "Certificat del $period ";
        $message = "El certificat del mes $period s'han penjat al portal";
        Setting::updateOrCreate(["key" => "subject_certificate"], ["val" => $subject]);
        Setting::updateOrCreate(["key" => "message_certificate"], ["val" => $message]);
    }

    public function upload()
    {
        Log::info('mensaje de llegada de certificado ',['data'=>request()->all()]);
        $rules = [
            "certificate" => 'required|mimes:pdf|max:10000',
            'period' => "unique_period_company"
        ];

        $validator = Validator::make(request()->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 422);
        }
        $this->setMessages(request("period"));

        $this->currentPathForCertificate = sprintf('%s/%s', 'certificate', request('period'));
        $certificateData = $this->splitAndValidatePDF(request()->file("certificate"));

        $this->processEachWorkerCertificate($certificateData);
        /*
        $this->processFullCertificateForAgency();
        */

        return response()->json([
            "success" => $this->success,
            "errors" => $this->arrayErrorsOnCheckCertificateFile,
            "errorsDNI" => $this->errorDNI,
            //"debug" => $certificateData,
        ]);
    }
    public function update(Request $request)
    {   
        $formData = $request->all();
       
        Log::info('mensaje',['data'=>$formData]);
        $period = $formData['period'];
        $companyId = $formData['company_id'];
        // $certificatePeriod = CertificatePeriod::where('period', $period)
        // ->where('company_id', $companyId)
        // ->first();
        $newPeriod = $formData['period_new'];
        $certificate = Certificate::where('company_id', $companyId)
        ->where('period', $period)
        ->update(['period' => $newPeriod]);

        if ($certificate) {
            // $certificatePeriod->period = $formData['period_new'];
            // $certificatePeriod->save();
            return response()->json(["message" => true]);
        }
        return response()->json(['message' => false]);
    }
    protected function splitAndValidatePDF(UploadedFile $file)
    {
        $this->success = true;

        $filename = $file->getPathname();

        $pdf = new Fpdi();

        $this->arrayErrorsOnCheckCertificateFile = [];

        $parser = new Parser();
        $data = [];

        try {
            $pageCount = $pdf->setSourceFile($filename);
        } catch (\Exception $exception) {
            //Log::error($exception->getMessage());

            array_push(
                $this->arrayErrorsOnCheckCertificateFile,
                'El PDF no es vàlid'
            );
            $this->success = false;

            return $data;
        }

        $this->currentWorkerDNI = null;
        $company = Company::find(request("company_id"));
        for ($i = 1; $i <= $pageCount; $i++) {

            $currentPage = new FPDI();
            $currentPage->AddPage();
            $currentPage->setSourceFile($filename);
            $currentPage->useTemplate($currentPage->importPage($i));

            $currentPageFilename = str_replace('.', '', \uniqid('', true)) . '.pdf';
            $tmpFilename = '/tmp/' .  $currentPageFilename;
            //$tmpFilename = public_path() . '/test' .  $currentPageFilename;
            $currentPage->Output($tmpFilename, 'F');


            $document = $parser->parseFile($tmpFilename);
            $text = $this->normalize($document->getText());

            // Get worker id

            preg_match(self::ID_REGEX, $text, $idMatches, 0, 20);
            if (empty($idMatches[0])) {
                array_push($this->arrayErrorsOnCheckCertificateFile, [
                    "La pagina $i del PDF no conté un ID de treballador vàlid"
                ]);
                continue;
            }

            $currentWorkerId = $idMatches[0];


            if ($this->currentWorkerDNI !== $currentWorkerId) {
                if ($this->currentWorkerDNI !== null) {
                    //Cerramos i guardamos CurrentWorkerFile

                    $this->closeAndSaveCurrentPDF($data, $this->currentWorkerDNI, $company, $this->currentWorkerDNI);
                }
                $this->currentWorkerDNI = $currentWorkerId;
                //Generamos nuevo CurrentWorkerFile
                $this->currentWorkerFile = new Fpdi();
                // add a page
                $this->currentWorkerFile->AddPage();
                // set the source file
                $this->currentWorkerFile->setSourceFile($tmpFilename);
                $this->currentWorkerFile->useTemplate($this->currentWorkerFile->importPage(1));
            } else {
                //Añadimos Pagina
                // add a page
                $this->currentWorkerFile->AddPage();
                // set the source file
                $this->currentWorkerFile->setSourceFile($tmpFilename);
                $this->currentWorkerFile->useTemplate($this->currentWorkerFile->importPage(1));
            }
            if ($i == $pageCount) {
                //Cerramos i guardamos CurrentWorkerFile
                $this->closeAndSaveCurrentPDF($data, $this->currentWorkerDNI, $company, $this->currentWorkerDNI);
            }
        }

        return $data;
    }

    public function closeAndSaveCurrentPDF(&$data, $nif, $company, $workerDNI)
    {
        $worker = Worker::whereDni($workerDNI)->first();
        if ($worker) {
            if (!DB::table("company_worker")->where(["company_id" => $company->id, "worker_id" => $worker->id])) {
                array_push(
                    $this->arrayErrorsOnCheckCertificateFile,
                    "El treballador amb DNI $workerDNI no treballa per la empresa $company->name"
                );

                array_push(
                    $this->errorDNI,
                    ["type" => "Treballador Trobat pero no empresa", "dni" => $workerDNI]
                );
            }
            $tmp = "/tmp/testfinal" . str_replace('.', '', \uniqid('', true)) . '.pdf';
            //$tmp = public_path() . "/testfinal" . str_replace('.', '', \uniqid('', true)) . '.pdf';
            $fullPath = $fileNameWithFullPath = sprintf(
                '%s/%s/%s',
                $this->currentPathForCertificate,
                Str::slug($company->name, '-'),
                str_replace('.', '', \uniqid('', true)) . '.pdf'
            );

            $this->currentWorkerFile->Output($tmp, "F");

            $data[] = [
                "s3File" => [
                    "path" => $fileNameWithFullPath,
                    "content" => file_get_contents($tmp)
                ],
                "certificate" => [
                    "worker_id" => $worker->id,
                    "company_id" => $company->id,
                    "document_file" => $fileNameWithFullPath,
                    "processed" => request("send_email") === 'true' ? now() : null,
                    "period" => request("period"),
                    "opened" => null,
                ]
            ];
        } else {
            array_push(
                $this->arrayErrorsOnCheckCertificateFile,
                "No s'ha trobat un treballador amb DNI $workerDNI en aquest arxiu de certificats"
            );
            array_push(
                $this->errorDNI,
                ["type" => "DNI no trobat", "dni" => $workerDNI]
            );
        }
    }
    /**
     * @param array $payrollData
     */
    public function processEachWorkerCertificate(array $payrollData)
    {
        try {
            DB::beginTransaction();
            foreach ($payrollData as $payroll) {
                Certificate::create($payroll["certificate"]);
                Storage::disk('s3')->put($payroll["s3File"]["path"], $payroll["s3File"]["content"]);
            }
            DB::commit();
            $this->processedEachWorkerSuccessfully = true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->processedEachWorkerSuccessfully = false;
            //Log::debug($exception->getMessage());
        }
    }

    protected function processFullCertificateForAgency()
    {
        $path = sprintf(
            '%s/%s',
            $this->currentPathForPeriod,
            'gestoria'
        );
        try {
            DB::beginTransaction();
            $newFilename = request()->file("certificate")->hashName();
            $fileNameWithFullPath = sprintf(
                '%s/%s',
                $path,
                $newFilename
            );
            Storage::disk('s3')->put(
                $path,
                request()->file("certificate")
            );
            CertificatePeriod::create([
                "user_id" => auth()->id(),
                "file" => $fileNameWithFullPath,
                "period" => request('period'),
                "company_id" => request('company')
            ]);
            DB::commit();
            $this->processedFullPayrollForAgency = true;
        } catch (\Exception $exception) {
            DB::rollBack();
            Storage::disk('s3')->deleteDirectory($this->currentPathForPeriod);
            $this->processedFullPayrollForAgency = false;
            //Log::debug($exception->getMessage());
        }
    }

    /**
     * @param string $text
     * @return string
     */
    private function normalize(string $text): string
    {
        $text = filter_var($text, FILTER_SANITIZE_STRING);
        $text = preg_replace("/[^A-Za-z0-9 ]/", '', $text);
        $text = str_replace(["\t", "\n"], '', $text);

        $previousLen = strlen($text);
        for ($j = 0; $j < 500; $j++) {
            $text = str_replace('  ', ' ', $text);
            if (strlen($text) === $previousLen) {
                break;
            }
            $previousLen = strlen($text);
        }

        return $text;
    }
}
