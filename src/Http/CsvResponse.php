<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\Response;

class CsvResponse extends Response
{
    /**
     * @var array
     */
    private array $data;

    /**
     * @var string
     */
    private string $filename;

    /**
     * CsvResponse constructor.
     * @param array $data
     * @param string $filename
     * @param int $status
     * @param array $headers
     */
    public function __construct(array $data = [], string $filename = 'koillection.csv', int $status = 200, array $headers = [])
    {
        parent::__construct('', $status, $headers);

        $this->data = $data;
        $this->filename = $filename;
        $this->render();
    }

    private function render()
    {
        $output = fopen('php://temp', 'r+');

        foreach ($this->data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $dataString = '';
        while ($line = fgets($output)) {
            $dataString .= $line;
        }

        $dataString .= fgets($output);

        $this->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $this->filename));

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/csv');
        }

        return $this->setContent($dataString);
    }
}
