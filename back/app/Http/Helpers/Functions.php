<?php

namespace App\Http\Helpers;

class Functions
{


    public static function pr($data, $die = '')
    {
        echo "<pre>" . print_r($data, true) . "</pre>";
        if ($die) die;
    }

    public static function diff(\DateTime $datetime1, \DateTime $datetime2 = null, $all = null)
    {
        if (!isset($datetime2)) {
            $datetime2 = new \DateTime('now');
        }

        $interval = $datetime1->diff($datetime2, false);
        $days = $interval->days;

        $interval->s = $datetime2->getTimestamp() - $datetime1->getTimestamp();
        $interval->i = floor($interval->s / 60);
        $interval->h = floor($interval->s / (60 * 60));
        $interval->d = $days;
        $interval->w = floor($days / 7);
        $interval->m = floor($days / $datetime1->format('t'));

        if (empty($all))
            return $interval->d;
        else  return $interval;
    }

    public static function getQuerySQL($query)
    {

        $Parrams = $query->getBindings();
        $arr_query = explode('?', $query->toSql());
        $SQL = '';
        foreach ($arr_query as $key => $pact) {
            if (key_exists($key, $Parrams)) {
                $SQL .= $pact . '\'' . $Parrams[$key] . '\'';
            }
        }

        return $SQL;
    }





    public static function getMessageForResultSavingData(int $resultCount, int $originalCount, array $massages)
    {
        $mark = $originalCount - $resultCount;
        switch ($mark) {
            //когда все записы новые
            case 0:
                $massage = sprintf($massages[0], $originalCount);

                $result = ['alert-type' => 'info', 'message' => $massage];
                return $result;

                break;

            //когда частично новые и частично уже были добавлены
            case ($originalCount > $mark && $mark > 0):

                $savedItems = $originalCount - $resultCount;
                $newItems = $originalCount - $savedItems;

                $massage = sprintf($massages[1], $originalCount, $savedItems, $newItems);
                $result = ['alert-type' => 'info', 'message' => $massage];

                return $result;

                break;

            //когдв все записи уже было добавлены ранее
            case ($originalCount):

                $massage = sprintf($massages[2], $originalCount);

                $result = ['alert-type' => 'success', 'message' => $massage];

                return $result;

                break;
        }

    }


    public static function checkResultMethodinsertOnDuplicateKey(int $resultCount, int $originalCount, array $massages)
    {

        switch ($resultCount) {

            case 0:
                $massage = sprintf($massages[0], $originalCount);

                $result = ['alert-type' => 'info', 'message' => $massage];
                return $result;

                break;


            case ($originalCount > $resultCount / 2):

                $savedItems = $originalCount - ($resultCount / 2);
                $newItems = $originalCount - $savedItems;

                $massage = sprintf($massages[1], $originalCount, $savedItems, $newItems);
                $result = ['alert-type' => 'info', 'message' => $massage];

                return $result;

                break;

            case ($originalCount == $resultCount / 2):

                $massage = sprintf($massages[2], $originalCount);

                $result = ['alert-type' => 'success', 'message' => $massage];

                return $result;

                break;
        }

    }


    /**
     * @param $key
     * @param $data
     * @return array
     */
    public static function group_by($key, $data)
    {
        $result = array();

        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }

    /**
     * @param $key
     * @param $data
     * @return array|\Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public static function group_by_one_element($key, $data)
    {
        $new =  (is_array($data))?
                   []
                  :
                   collect();

        foreach ($data as $item) {

            if (is_array($item))
                $new[$item[$key]] = $item;
            else if (is_object($item)) {
                $new[$item->{$key}] = $item;
            }

        }

        return $new;
    }




    /**
     * @param $pattern
     * @param $input
     * @param int $flags
     * @return array
     */
    public static function preg_grep_keys($pattern, $input, $flags = 0)
    {

        return array_intersect_key($input, array_flip(preg_grep($pattern, array_keys($input), $flags)));
    }

    /**
     * @param $pattern
     * @param $replacement
     * @param array $input
     * @return array
     */
    public static function preg_replace_keys($pattern, $replacement, array $input)
    {

        $keys = array_keys($input);
        $values = array_values($input);
        $result = preg_replace($pattern, $replacement, $keys);
        $output = array_combine($result, $values);

        return $output;
    }


    public static    function change_key( $arrays, $old_key, $new_key, $is_two_dimensional_array=false) {

        if($is_two_dimensional_array==false)
            $arrays = [$arrays];


        foreach ( $arrays as $key => $array) {
            if (!array_key_exists($old_key, $array)){
                dump('KEY |'.$old_key."| NOT exist. ".__METHOD__);
                return $array;}

            $keys = array_keys($array);
            $keys[array_search($old_key, $keys)] = $new_key;

            $arrays[$key]=  array_combine( $keys, $array );
        }

        return $arrays;
    }

    /**
     * @param $path
     * @param $data
     */
    public static function file_put_contents(string $file_name, string $data, bool $if_replace_file = false)
    {

        if ($if_replace_file) {

            if (file_exists($file_name))
                dump($file_name . ' file exists!');

            $f = fopen($file_name, 'w');
            fwrite($f, $data);
            fclose($f);

        } else if (!$if_replace_file && !file_exists($file_name)) {

            $f = fopen($file_name, 'w');
            fwrite($f, $data);
            fclose($f);
        }

    }

    public static function file_get_contents(string $path)
    {

        if (file_exists($path)) {

            $f = fopen($path, 'r');
            $content = fread($f, filesize($path));
            fclose($f);

            return $content;
        } else
            dump("file: {$path} not exists!");
    }


    /**
     * @param string $str
     * @return string
     */
    public static function convert_str_to_translite(string $str){
        $map = [
            'ж' => 'zh',
            'ч' => 'ch',
            'щ' => 'sch',
            'ш' => 'sh',
            'ю' => 'yu',
            'ё' => 'e',
            'я' => 'ya',
            'э' => 'e',
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'з' => 'z',
            'и' => 'i',
            'й' => 'i',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ъ' => '',
            'ь' => '',
            'ы' => 'y',

            'Ж' => 'Zh',
            'Ч' => 'Ch',
            'Щ' => 'Sch',
            'Ш' => 'Sh',
            'Ю' => 'Yu',
            'Ё' => 'E',
            'Я' => 'Ya',
            'Э' => 'E',
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'I',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ъ' => '',
            'Ь' => '',
            'Ы' => 'Y',

            'є' => 'e',
            'Є' => 'E',
            'і' => 'i',
            'І' => 'I',
            'ї' => 'i',
            'Ї' => 'I',
            '\'' => '',
        ];

       return   strtr($str,$map);
    }

}
