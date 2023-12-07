<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Setting;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Mail\Payrolls;
use App\Models\WorkerDni;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Mail;

class PayrollController extends Controller
{
    const NIF_REGEX = '/NIF. (\w+)/m';
    const ID_REGEX = '/[0-9A-Z][0-9]{7,7}[A-Za-z]/';
    const NAME_REGEX = '/(\w+)/m';
  
    protected $processedEachWorkerSuccessfully;
    protected $processedFullPayrollForAgency;
    protected $currentPathForPeriod;
    protected $arrayErrorsOnCheckPayrollFile;
    protected $arrayErrorsworkerIdPayrollFile;
    public function construct()
    {
        $this->processedEachWorkerSuccessfully = false;
        $this->processedFullPayrollForAgency = false;
        $this->arrayErrorsOnCheckPayrollFile = [];
        $this->arrayErrorsworkerIdPayrollFile = [];
    }

    public function paginatedForReport()
    {
     //   Log::info('mensaje 5');
        if (request()->wantsJson()) {
            $itemsPerPage = (int) request('itemsPerPage');
            $payrolls = Payroll::filtered();
            return response()->json(["success" => true, "data" => $payrolls->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10)]);
        }
    }

    public function allFiltered()
    {
      //  Log::info('mensaje 6');
        if (request()->wantsJson()) {
            $payrolls = Payroll::filtered();
            return response()->json(["success" => true, "data" => $payrolls->get()]);
        }
    }
    public function markOpened(int $id)
    {
      //  Log::info('mensaje 4');
        if (request()->wantsJson()) {
            try {
                DB::beginTransaction();
                $payroll = Payroll::find($id);
                if (!$payroll) {
                    return response()->json(["success" => false], 404);
                }

                if ($payroll) {
                    $payroll->opened = Carbon::now();
                    $payroll->save();
                    DB::commit();
                    return response()->json(["success" => true]);
                }
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json(["success" => false, "data" => $exception->getMessage()]);
            }
        }
    }
    public function generateS3SignedUrl()
    {
      //  Log::info('mensaje 3');
        if (request()->wantsJson()) {
            try {
                $url = Storage::disk('s3')->temporaryUrl(
                    request('document_file'),
                    Carbon::now()->addMinutes(Payroll::TIME_SIGNATURE_S3_TEMPORARY_URL)
                );
                return response()->json(["success" => true, "signed_url" => $url]);
            } catch (\Exception $exception) {
                return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
            }
        }
    }

    private function setMessages($perdiod)
    {
       // Log::info('mensaje 12');
        $subject = "Nòmines del mes ";
        $message = "Les nòmines del mes ";
        switch (explode("-", request("period"))[1]) {
            case "1":
                $subject .= "Gener";
                $message .= "Gener";
                break;
            case "2":
                $subject .= "Febrer";
                $message .= "Febrer";
                break;
            case "3":
                $subject .= "Març";
                $message .= "Març";
                break;
            case "4":
                $subject .= "Abril";
                $message .= "Abril";
                break;
            case "5":
                $subject .= "Maig";
                $message .= "Maig";
                break;
            case "6":
                $subject .= "Juny";
                $message .= "Juny";
                break;
            case "7":
                $subject .= "Juliol";
                $message .= "Juliol";
                break;
            case "8":
                $subject .= "Agost";
                $message .= "Agost";
                break;
            case "9":
                $subject .= "Setembre";
                $message .= "Setembre";
                break;
            case "10":
                $subject .= "Ocutbre";
                $message .= "Ocutbre";
                break;
            case "11":
                $subject .= "Novembre";
                $message .= "Novembre";
                break;
            case "12":
                $subject .= "Desembre";
                $message .= "Desembre";
                break;
        }
        $message .= " s'han penjat al portal";
        Setting::where("key", "subject_payrolls")->update(["val" => $subject]);
        Setting::where("key", "message_payrolls")->update(["val" => $message]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function upload()
    {
     //   Log::info('mensaje de llegada de payroll ',['data'=>request()->all()]);
        try {
            $rules = [
                "payroll" => 'required|mimes:pdf|max:10000',
              //  'period' => "unique_period_company"  //se va ha permitir que se guarden varias nominas con el mismo periodo
            ];

            $validator = Validator::make(request()->all(), $rules);
            if ($validator->fails()) {
                return response()->json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 422);
            }
            $this->setMessages(request("period"));

            $this->currentPathForPeriod = sprintf('%s/%s', 'payrolls', request('period'));
            $payrollData = $this->splitAndValidatePDF(request()->file("payroll"));

            if (count((array) $this->arrayErrorsOnCheckPayrollFile) > 0) {
                return response()->json(["message" => "error procesando las nóminas", "errors" => $this->arrayErrorsOnCheckPayrollFile], 400);
            } else {

                $this->processEachWorkerPayroll($payrollData);

                if ($this->processedEachWorkerSuccessfully) {
                    $this->processFullPayrollForAgency();
                }

                if ($this->processedFullPayrollForAgency) {
                    if (count((array) $this->arrayErrorsworkerIdPayrollFile) > 0) {

                        array_unshift($this->arrayErrorsworkerIdPayrollFile, [
                            sprintf("%s: %s", 'Se procesó la nómina parcialmente, faltó:', '')
                        ]);
                        
                        return response()->json(["message" => "Se procesó la nómina parcialmente, faltó:", "errors" => $this->arrayErrorsworkerIdPayrollFile], 400);
                    } else {

                        return response()->json(["message" => "success"]);
                    }
                   
                }
            }
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 400);
        }
    }
    public function update(Request $request)
    {   
        $formData = $request->all();
        Log::info('mensaje',['data'=>$formData]);
        
        $period = $formData['period'];
      
        $payrollPeriod = PayrollPeriod::where('period', $period)
        ->where('company_id', $formData['company_id'])
        ->first();

        if ($payrollPeriod) {
     
            $payrollPeriod->period = $formData['period_new'];
            $payrollPeriod->save();
            $newPeriod = $formData['period_new'];
            $companyId = $formData['company_id'];
            Payroll::where('company_id', $companyId)
            ->where('period', $period)
            ->update(['period' => $newPeriod]);
            return response()->json(["message" => true]);
        }
        return response()->json(['message' => false]);
    }

    public function delete(Request $request)
    {
        $formData = $request->all();
        $period = $formData['period'];
        $companyId = $formData['company_id'];

        // Eliminar registros de la tabla Payrolls
        Payroll::where('company_id', $companyId)
            ->where('period', $period)
            ->delete();

        // Eliminar registros de la tabla PayrollPeriod
        PayrollPeriod::where('company_id', $companyId)
            ->where('period', $period)
            ->delete();

        return response()->json(["message" => true]);
    }

    /**
     *
     * validamos el PDF para asegurarnos que podemos procesar todos los datos
     *
     * @param UploadedFile $file
     * @return array
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    protected function splitAndValidatePDF(UploadedFile $file)
    {
    //    Log::info('mensaje 2');
        $filename = $file->getPathname();

        $pdf = new Fpdi();

        $this->arrayErrorsOnCheckPayrollFile = [];
        $this->arrayErrorsworkerIdPayrollFile = [];
        //Error found

        $parser = new Parser();
        $payrollData = [];
        try {
            $pageCount = $pdf->setSourceFile($filename);
        } catch (\Exception $exception) {
            //Log::error($exception->getMessage());
            array_push($this->arrayErrorsOnCheckPayrollFile, [
                'El PDF no es vàlid'
            ]);

            return $payrollData;
        }
        //Log::info("seguimos");

        // Split each page into a new PDF
        for ($i = 1; $i <= $pageCount; $i++) {
            $newPdf = new FPDI();
            $newPdf->AddPage();
            $newPdf->setSourceFile($filename);
            $newPdf->useTemplate($newPdf->importPage($i));

            $newFilename = str_replace('.', '', \uniqid('', true)) . '.pdf';
            $tmpFilename = '\tmp' . $newFilename;
            // $tmpFilename = public_path('temp') . '/' . $newFilename;
            $newPdf->Output($tmpFilename, 'F');

            $document = $parser->parseFile($tmpFilename);
            $text = $document->getText();

            // Get company NIF
            preg_match(self::NIF_REGEX, $text, $cifMatches);
            if (count($cifMatches) < 1) {
                array_push($this->arrayErrorsOnCheckPayrollFile, [
                    'No es pot llegir el pdf'
                ]);
                break;
            }
            if (empty($cifMatches[1])) {
                array_push($this->arrayErrorsOnCheckPayrollFile, [
                    'El PDF no conté un NIF vàlid'
                ]);
            }
            $cif = $cifMatches[1];

            // Get worker id
            preg_match(self::ID_REGEX, str_replace("\\", "", str_replace(["\n", "\r"], "", $text)), $idMatches);
            //Log::debug($idMatches);
        
            if (empty($idMatches[0])) {
                array_push($this->arrayErrorsOnCheckPayrollFile, [
                    'El PDF no conté un ID de treballador vàlid'
                ]);
                continue;
            }

            $workerId = $idMatches[0];
            $company = Company::whereCif($cif)->first();
            if (!$company) {
                array_push($this->arrayErrorsOnCheckPayrollFile, [
                    sprintf("%s: %s", 'No s\'ha trobat una empresa per al treballador amb DNI en aquest arxiu de nòmines', $workerId)
                ]);
                continue;
            }
            
            if ($company->id !== (int) request("company_id")) {
                $companySelected = Company::select("name")->find(request("company_id"));
                array_push($this->arrayErrorsOnCheckPayrollFile, [
                    sprintf("%s %s", "L'arxiu pujat no pertany a l'empresa", $companySelected->name)
                ]);
                break;
            }

            $worker = Worker::whereDni($workerId)->first();

              if (!$worker) {
                array_push($this->arrayErrorsworkerIdPayrollFile, [
                    sprintf("%s: %s", 'No s\'ha trobat un treballador amb DNI en aquest arxiu de nòmines', $workerId)
                ]);

                //guardo en la tabla 
               // preg_match(self::NAME_REGEX, str_replace("\\", "", str_replace(["\n", "\r"], "", $text)), $name_workers);
               preg_match(self::NAME_REGEX, $text, $name_workers);
               // Log::info($text);
               // Log::info($name_workers);

                WorkerDni::updateOrCreate([
                    "dni"        => $workerId,
                    "period"     => request("period"),
                    "company_id" => request("company_id"),
                ], [
                     "name"         => $name_workers[0],
                     "company_name" => $company->name
                ]);

                continue;
            }

            // PODEMOS CREAR LA NÓMINA Y EL REGISTRO EN BASE DE DATOS
            $fileNameWithFullPath = sprintf(
                '%s/%s/%s',
                $this->currentPathForPeriod,
                Str::slug($company->name, '-'),
                $newFilename
            );

            array_push($payrollData, [
                "s3File" => [
                    "path" => $fileNameWithFullPath,
                    "content" => file_get_contents($tmpFilename)
                ],
                "payroll" => [
                    "worker_id" => $worker->id,
                    "company_id" => $company->id,
                    "document_file" => $fileNameWithFullPath,
                    "processed" => request("send_email") === 'true' ? now() : null,
                    "period" => request("period"),
                    "opened" => null,
                ]
            ]);
        }
        return $payrollData;
    }

    /**
     * @param array $payrollData
     */
    public function processEachWorkerPayroll(array $payrollData)
    {
      //  Log::info('mensaje 7');
        try {
            DB::beginTransaction();
            foreach ($payrollData as $payroll) {
                Payroll::create($payroll["payroll"]);
                Storage::disk('s3')->put($payroll["s3File"]["path"], $payroll["s3File"]["content"]);
            }
            DB::commit();
            $this->processedEachWorkerSuccessfully = true;
        } catch (\Exception $exception) {
            DB::rollBack();
            Storage::disk('s3')->deleteDirectory($this->currentPathForPeriod);
            $this->processedEachWorkerSuccessfully = false;
        }
    }

    /**
     * generamos y relacionamos el archivo de nóminas completo en S3 y BD
     */
    protected function processFullPayrollForAgency()
    {
     //   Log::info('mensaje 8');
        $path = sprintf(
            '%s/%s',
            $this->currentPathForPeriod,
            'gestoria'
        );
        try {
            DB::beginTransaction();
            $newFilename = request()->file("payroll")->hashName();
            $fileNameWithFullPath = sprintf(
                '%s/%s',
                $path,
                $newFilename
            );
            Storage::disk('s3')->put(
                $path,
                request()->file("payroll")
            );
            PayrollPeriod::create([
                "user_id" => auth()->id(),
                "file" => $fileNameWithFullPath,
                "period" => request('period'),
                "company_id" => request('company_id')
            ]);
            DB::commit();
            $this->processedFullPayrollForAgency = true;
        } catch (\Exception $exception) {
            DB::rollBack();
            Storage::disk('s3')->deleteDirectory($this->currentPathForPeriod);
            $this->processedFullPayrollForAgency = false;
        }
    }

    /**
     * @param string $text
     * @return string
     */
    private function normalize(string $text): string
    {
       // Log::info('mensaje 9');
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

    public function updateFile($id)
    {
      //  Log::info('mensaje 10');
        $p = Payroll::where('id', $id)->first();
        $route = "";
        $route_split = explode("/", $p->document_file);
        foreach ($route_split as $index => $text) {
            if ($index < count($route_split) - 1) {
                $route .= $text;
                $route .= "/";
            }
        };
        $file = request("file");

        Storage::disk('s3')->put($route, $file);
        $route .= $file->hashName();
        $p->document_file = $route;
        $p->save();

        return [
            "success" => true,
            "payroll" => $p
        ];
    }
}
