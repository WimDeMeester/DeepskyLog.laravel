<?php

namespace App\Http\Controllers;

use Xinax\LaravelGettext\Facades\LaravelGettext;

class LanguageController extends Controller
{
    /**
     * Changes the current language and returns to previous page
     *
     * @return Redirect
     */
    public function changeLang()
    {
        LaravelGettext::setLocale(request('language'));
        return back();
    }
}
?>
