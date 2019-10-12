<?php

class Class_Fuzzy_String_Search
{
    // словарь
    public $voc;

    public function __construct($voc)
    {
  	$this->voc = $voc;
    }

    // Возвращает транслитерацию аргумента
    private function translitIt($str)
    {
        $translit_rules = [
            "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
            "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
            "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
            "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
            "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
            "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
            "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
            "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
            "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
            "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
            "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
            "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
            "ы"=>"yi","ь"=>"'","э"=>"e","ю"=>"yu","я"=>"ya"
        ];
        return strtr($str, $translit_rules);
    }

    public function correctWord($words)
    {
    	$word_list = [];
	foreach($this->voc as $ru_word) {
            $word_translit[mb_strtoupper($ru_word)] = $this->translitIt(mb_strtoupper($ru_word));
  	}

        $i = 0;
        while($i < count($words)) {
            if(isset($word_list[$words[$i]])) {
                $correct[] = $words[$i];
            } else {
		$enteredWord = mb_strtoupper($this->translitIt($words[$i]));
		$possibleWord = [];
		foreach($word_translit as $n => $k) {
                    if(levenshtein(metaphone($enteredWord), metaphone($k)) < (mb_strlen(metaphone($enteredWord))/2)+1) {
			if(levenshtein($enteredWord, $k) < mb_strlen($enteredWord)) {
                            $possibleWord[$n] = $k;
			}
                    }
		}

		// подобие
                $similarity = 0;
                // подобие метафонов
                $meta_similarity = 0;
                // минимальное расстояние Левенштейна
		$min_levenshtein = 1000;
                // минимальное расстояние Левенштейна метафонов
		$meta_min_levenshtein = 1000;

                // минимальное расстояние Левенштейна
                if(count($possibleWord)) {
                    foreach($possibleWord as $n) {
			$min_levenshtein = min($min_levenshtein, levenshtein($n, $enteredWord));
                    }

                    // максимальное значение подобности слов
                    foreach($possibleWord as $n) {
                        if(levenshtein($k, $enteredWord) == $min_levenshtein) {
                            $similarity = max($similarity, similar_text($n, $enteredWord));
                        }
                    }

                    // проверка всего слова
                    $result = [];
                    foreach($possibleWord as $n=>$k) {
                        if(levenshtein($k, $enteredWord) <= $min_levenshtein) {
                            if(similar_text($k, $enteredWord) >= $similarity) {
				$result[$n] = $k;
                            }
                        }
                    }

                    foreach($result as $n) {
			$meta_min_levenshtein = min($meta_min_levenshtein, levenshtein(metaphone($n), metaphone($enteredWord)));
                    }

                    // максимальное значение подобности слов
                    foreach($result as $n) {
                        if(levenshtein($k, $enteredWord) == $meta_min_levenshtein) {
                            $meta_similarity = max($meta_similarity, similar_text(metaphone($n), metaphone($enteredWord)));
                        }
                    }

                    // метафон (similar_text - алгоритм Оливера)
                    $meta_result = [];
                    foreach($result as $n=>$k) {
			if(levenshtein(metaphone($k), metaphone($enteredWord)) <= $meta_min_levenshtein) {
                            if(similar_text(metaphone($k), metaphone($enteredWord)) >= $meta_similarity) {
				$meta_result[$n] = $k;
                            }
                        }
                    }
                    $correct[] = array_keys($meta_result);
                }
            }
            $i++;
        }
        return $correct;
    }
}