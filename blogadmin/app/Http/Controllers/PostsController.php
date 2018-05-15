<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Curl\Curl;

class PostsController extends Controller
{
    public function createArticle(Request $request)
    {
        $domain = $request->input('domain');
        $title = $request->input('title');
        $content = $request->input('content');

        if (empty($domain) || empty($title) || empty($content)) {
            return response()->json(['status' => 'error', 'message' => 'some input empty']);
        }

        $filename = "/home/www/article/$domain-" . time() . '_' . rand(1,5000);
        file_put_contents($filename, $content);
        shell_exec("cd /home/www/$domain && wp post create $filename --post_title=\"$title\" --post_status='publish'");

        return response()->json(['status' => 'done']);
    }

    public function saveFile(Request $request)
    {
        $fileSrc = $request->input('filesrc');

        $ext = 'jpg';
        $oldFilename = last(explode('/', $fileSrc));

        if (strstr($oldFilename, '.')) {
            $ext = last(explode(".", $oldFilename));
        }

        $filename = time() . rand(1000,5000) . '.' . $ext;
        $subPath = "files/" . date('Ymd', time()) . "/$filename";

        Storage::disk('public')->put($subPath, file_get_contents($fileSrc));

        return url('') . Storage::url($subPath);
    }

}