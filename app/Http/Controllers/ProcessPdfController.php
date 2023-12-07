<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Payroll;
use App\Models\Worker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class ProcessPdfController extends Controller
{
    const NIF_REGEX = '/NIF (\w+)/m';
    const ID_REGEX = '/(\w+) N A\s?FILIA\s?CION/um';


    public function form()
    {
        return view("nominas");
    }

    public function upload()
    {
        $this->splitPDF();
    }

    protected function splitPDF()
    {
        $file = request()->file("pdf");
        $filename = $file->getPathname();

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($filename);

        $parser = new \Smalot\PdfParser\Parser();

        // Split each page into a new PDF
        for ($i = 1; $i <= $pageCount; $i++) {
            $newPdf = new FPDI();
            $newPdf->AddPage();
            $newPdf->setSourceFile($filename);
            $newPdf->useTemplate($newPdf->importPage($i));

            $newFilename = str_replace('.', '', \uniqid('', true)) . '.pdf';
            $tmpFilename = '/tmp/' . $newFilename;
            $newPdf->Output($tmpFilename, 'F');

            $document = $parser->parseFile($tmpFilename);
            $text = $this->normalize($document->getText());

            // Get company NIF
            preg_match(self::NIF_REGEX, $text, $nifMatches);
            if (empty($nifMatches[1])) {
                throw new \InvalidArgumentException('Invalid PDF, does not contain NIF');
            }
            $nif = $nifMatches[1];
            //Log::info("Company NIF: " . $nif);

            // Get worker id
            preg_match(self::ID_REGEX, $text, $idMatches);
            if (empty($idMatches[1])) {
                throw new \InvalidArgumentException('Invalid PDF, does not contain worker ID');
            }
            $workerId = $idMatches[1];
            //Log::info("WorkerID: " . $workerId);

            $company = Company::whereNif($nif)->first();
            if (!$company) {
                var_dump($nif);
                die;
                continue;
            }
            $worker = Worker::whereIdNumber($workerId)->first();
            if (!$worker) {
                var_dump($workerId);
                die;
                continue;
            }
            Payroll::create([
                "worker_id" => $worker->id,
                "company_id" => $company->id,
                "document_file" => $newFilename
            ]);
            Storage::disk("public")->put("payrolls/" . $newFilename, file_get_contents($tmpFilename));
        }
    }

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
