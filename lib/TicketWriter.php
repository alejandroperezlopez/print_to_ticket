<?php 
/**
* Clase pare imprimir tickets.
*/
class TicketWriter
{   
    private $output;
    private $anchopapel;
    private $sin_comandos;

    public function __construct($ancho = 45, $comandocorte = null, $comandos = FALSE)
    {
        $this->output = '';
        $this->anchopapel = $ancho;
        $this->sin_comandos = $comandos;
        $this->comandocorte = ($comandocorte) ? a : '27.105';
    }

    public function add_text_line($text = '', $breake = TRUE, $center = FALSE)
    {
        $text = substr($text, 0, $this->anchopapel);
        if ($text != '') {
            if ($center) {
                $this->output .= $this->text_center($text);
            } else {
                $this->output .= $text;
            }            
        } 
        if ($breake) {
            $this->add_line_break();
        }                
    }

    public function add_bold_text_line($text = '', $brake = TRUE, $center = FALSE)
    {
        $text = substr($text, 0, $this->anchopapel);
        $text = chr(27) . chr(69) . chr(49) . $text . chr(27) . chr(69) . chr(48);
        if ($text != '') {
            if ($center) {
                $this->output .= $this->text_center($text);
            } else {
                $this->output .= $text;
            }            
        } 
        if ($brake) {
            $this->add_line_break();
        }           
    }

    public function add_big_text_line($text = '', $breake = TRUE, $center = FALSE)
    {
        if ($text != '') {
            if ($center) {
                $this->output .= $this->text_center($text);
            } else {
                $this->output .= $text;
            }            
        } 

        if ($breake) {
            $this->add_line_break();
        }                  
    }

    public function add_line_break($n = 1)
    {
        for ($i=0; $i < $n; $i++) { 
            $this->output .= "\n";
        }        
    }

    public function add_line_splitter($splitter = '-')
    {
        $line = '';
        for ($i = 0; $i < $this->anchopapel; $i++) {
            $line .= $splitter;
        }

        $this->output .= $line . "\n";
    }

    public function add_text_multiline($text)
    {
        if ($this->sin_comandos) {
            $this->output .= $text;
        } else {
            $this->output .= chr(27) . chr(33) . chr(56) . $text . chr(27) . chr(33) . chr(1);
        }
    }

    public function add_line_label_value($label, $value, $align = '')
    {
        $texto = $label;
        $ancho = $this->anchopapel - strlen($label);

        $value = substr($value, 0, $ancho);
        $texto .= sprintf('%' . $align . $ancho . 's', $value);

        $this->output .= $texto;
        $this->add_line_break();
    }

    public function add_bcode_line($text = '')
    {
        $barcode = '';
        $barcode .= chr(27) . chr(97) . chr(49); #justification n=0,48 left n=2,49 center n=3,50 right
        $barcode .= chr(29) . chr(104) . chr(70); #barcode height in dots n=100, 1 < n < 255
        $barcode .= chr(29) . chr(119) . chr(2); #barcode width multiplier n=1, n=2, 
        $barcode .= chr(29) . chr(72) . chr(50); #barcode HRI position n=1,49 above n=2,50 below 
        $barcode .= chr(29) . chr(107) . chr(4) . $text . chr(0);
        $this->output .= $barcode;
    }

    public function text_center($word = '', $ancho = FALSE)
    {
        if (!$ancho) {
            $ancho = $this->anchopapel;
        }

        if (strlen($word) == $ancho) {
            return $word;
        } else if (strlen($word) < $ancho) {
            return $this->text_center_aux($word, $ancho);
        }

        $result = '';
        $nword = '';
        foreach (explode(' ', $word) as $aux) {
            if ($nword == '') {
                $nword = $aux;
            } else if (strlen($nword) + strlen($aux) + 1 <= $ancho) {
                $nword = $nword . ' ' . $aux;
            } else {
                if ($result != '') {
                    $result .= "\n";
                }

                $result .= $this->text_center_aux($nword, $ancho);
                $nword = $aux;
            }
        }
        if ($nword != '') {
            if ($result != '') {
                $result .= "\n";
            }

            $result .= $this->text_center_aux($nword, $ancho);
        }

        return $result;
    }

    private function text_center_aux($word = '', $ancho = 40)
    {
        $symbol = " ";
        $middle = round($ancho / 2);
        $length_word = strlen($word);
        $middle_word = round($length_word / 2);
        $last_position = $middle + $middle_word;
        $number_of_spaces = $middle - $middle_word;
        $result = sprintf("%'{$symbol}{$last_position}s", $word);
        for ($i = 0; $i < $number_of_spaces; $i++) {
            $result .= "$symbol";
        }
        return $result;
    }

    public function paper_cut_line()
    {
        if ($this->comandocorte) {
            $aux = explode('.', $this->comandocorte);
            if ($aux) {
                foreach ($aux as $a) {
                    $this->output .= chr($a);
                }

                $this->add_line_break();
            }
        } 
    }

    public function toString()
    {
        $this->add_line_break(4);
        $this->paper_cut_line();
        
        return $this->output;
    }
}