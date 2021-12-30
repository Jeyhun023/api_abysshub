<?php

namespace App\Helpers;

class Helper
{
    public static function get_description(string $content)
    {
        $description = preg_replace('/<(pre)(?:(?!<\/\1).)*?<\/\1>/s', '·', $content);
        $description = strip_tags($description);
        $description = str_replace('  ', ' ', $description);
        $description = substr($description, 0, 246);
        $description = preg_replace('/\xB0/u', '', $description);
        $description = preg_replace('/\s\s+/', ' ', $description);
        $description = trim($description);

        return $description;
    }
    
    public static function get_explode($content)
    {
        $data = [];
        if($content != null){
            $data = explode(',' , $content);
            $data = array_map('trim', $data);
        }
        return $data;
    }
}