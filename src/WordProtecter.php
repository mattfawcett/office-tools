<?php
namespace WebmergeOfficeTools;

interface WordProtecter
{
    /**
     * Password protect (encrypt) a word document in a way Office can open
     * if supplied with the password
     **/
    public function passwordProtectWordFile(string $filePath, string $outputFilePath, string $password): void;

    public function implementationName(): string;
}
