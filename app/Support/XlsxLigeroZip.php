<?php

namespace App\Support;

/** Escritor ZIP mínimo (sin la extensión zip de PHP): comprime con zlib si está disponible. */
class XlsxLigeroZip
{
    private string $datos = '';
    private string $directorio = '';
    private int $entradas = 0;

    public function agregar(string $nombre, string $contenido): void
    {
        $crc = crc32($contenido);
        $tamano = strlen($contenido);
        $comprimido = function_exists('gzdeflate') ? gzdeflate($contenido, 6) : false;
        $metodo = $comprimido !== false ? 8 : 0;
        $cuerpo = $comprimido !== false ? $comprimido : $contenido;

        $t = getdate();
        $hora = ($t['hours'] << 11) | ($t['minutes'] << 5) | intdiv($t['seconds'], 2);
        $fecha = (($t['year'] - 1980) << 9) | ($t['mon'] << 5) | $t['mday'];

        $desplazamiento = strlen($this->datos);
        $comun = pack('vvvvvVVVv', 20, 0x0800, $metodo, $hora, $fecha, $crc, strlen($cuerpo), $tamano, strlen($nombre));
        $this->datos .= "PK\x03\x04" . $comun . pack('v', 0) . $nombre . $cuerpo;
        $this->directorio .= "PK\x01\x02" . pack('v', 20) . $comun . pack('vvvvVV', 0, 0, 0, 0, 0, $desplazamiento) . $nombre;
        $this->entradas++;
    }

    public function contenido(): string
    {
        return $this->datos . $this->directorio
            . "PK\x05\x06" . pack('vvvvVVv', 0, 0, $this->entradas, $this->entradas, strlen($this->directorio), strlen($this->datos), 0);
    }
}
