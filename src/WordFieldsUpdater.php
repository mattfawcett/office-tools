<?php
namespace WebmergeOfficeTools;

interface WordFieldsUpdater
{
    public function updateFieldsInWordDocument(string $filePath, string $outputFilePath): void;

    public function implementationName(): string;
}
