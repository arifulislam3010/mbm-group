<?php

namespace App\Interfaces\AdminSettings;

interface LangRepositoryInterface
{
    public function language(array $request);
    public function addLanguage(array $request);
    public function updateLanguage(array $request);
    public function deleteLanguage(int $id);

    public function addLanguageValue(array $request);
    public function updateLanguageValue(array $request);
    public function deleteLanguageValue(int $id);
}
