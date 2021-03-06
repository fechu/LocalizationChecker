<?php

namespace LocalizationChecker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use SM\Lexer\Lexer;
use SM\String\UTF16Decoder;
use LocalizationChecker\Lexer\TranslationEntryToken;
use LocalizationChecker\Lexer\CommentToken;
use LocalizationChecker\File\StringsFile;
use \SplFileInfo;

/**
 * The strings command handles parsing and comparing of .strings files.
 *
 * @author Sandro Meier
 */
class StringsCommand extends Command
{
    /**
     * Reference to the output interface object which can be used to output something
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * Contains an associatve array which has the file paths as keys and the according
     * StringsFile object as the value.
     */
    protected $files;

    protected function configure()
    {
        $this->setName('check:strings');
        $this->setDescription('Check the contents of *.strings files.');
        $this->addArgument(
            'files',
            InputArgument::IS_ARRAY,
            'The files you want to check. The first file is taken as the reference.'
        );

        $this->configureHelp();
    }

    protected function configureHelp()
    {
        $this->setHelp(<<<EOF
The <info>%command.name%</info> validates .strings (iOS/Mac) localization files. 

Example:

    <info>php %command.full_name% localizable.strings</info>

This will validate the syntax of the localizable.strings file. If you supply multiple files, these
files will be checked for completness. Example:

    <info>php %command.full_name% en.lproj/localizable.strings fr.lproj/localizable.strings</info>

Both files will be checked for correct syntax. When that check finishes successfully, it will also
check if for each key in en.lproj/localizable.strings that the according key exists in 
fr.lproj/localizable. 

EOF
        );
    }
    

    ////////////////////////////////////////////////////////////////////////
    // Execution
    ////////////////////////////////////////////////////////////////////////

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Store reference to output interface object
        $this->output = $output;

        $errorCount = 0;
        $files = $input->getArgument('files');
        if (!$files) {
            $output->writeln("<error>No files to check</error>");
            return -1;
        }

        // Parse the files.
        $errorCount += $this->parseFiles($files);

        if ($errorCount == 0) {
            // Check if all keys of the first file exist in the rest of the files.
            $errorCount += $this->checkTranslationKeys($files[0]);
        }

        // Exit code should be number of errors.
        return $errorCount;
    }

    /**
     * Checks the keys in the translation files
     *
     * @param string $baseFile  The path to the file which should be used as the reference.
     *                          The other files get checked against this file.    
     *
     * @return int The number of missing keys.
     */
    protected function checkTranslationKeys($baseFilePath)
    {
        // Initialization
        $errorCount = 0;
        $baseFile = $this->files[$baseFilePath];

        $keysToCheck = $baseFile->getTranslationKeys();
        
        foreach ($this->files as $path => $file){
            // Check if the file contains all the keys
            $containsAllKeys = $file->containsTranslationKeys($keysToCheck);
            if (!$containsAllKeys) {
                // Do more investigation on that file
                $this->output->writeln(
                    '<error>File "' . $path . '" has missing keys:</error>'
                );

                foreach ($keysToCheck as $key){
                    if(!$file->containsTranslationKeys($key)) {
                        $errorCount++;
                        $this->output->writeln(" - " .  $key );
                    }
                }
            }
        }
        
        return $errorCount;
    }
    

    /**
     * Parses an array of files
     *
     * @param array  $files  An array of paths to files.
     * @return The amount of files where errors occured.
     */
    protected function parseFiles($files)
    {
        // Initialzation
        $errorCount = 0;

        // Reset files array
        $this->files = array();

        // Parse all files
        foreach($files as $file) {
            $result = $this->parseFile($file);

            // Check if the parsing failed.
            if ($result == false) {
                $errorCount++;
            }
            else {
                // Add the result to the tokens array
                $stringsFile = new StringsFile($result);
                $this->files[$file] = $stringsFile;
            }
        }

        return $errorCount;
    }
    

    /**
     * Parses the .strings file at the given path.
     *
     * @param string    $path   The path to the file that should be parsed. If the file does
     *                          not exist, an InvalidArgumentException is thrown.
     * 
     * @return array|bool       The array of tokens if the lexer could sucessfully tokenize the 
     *                          file or false if tokenizing the file failed.
     *
     * @throws InvalidArgumentException If the file at the given path does not exist.
     */
    protected function parseFile($path)
    {
        $fileInfo = new SplFileInfo($path);

        // Check if the given file exists
        if(!$fileInfo->isFile()) {
            throw new \InvalidArgumentException(
                "File at path \"" . $path . "\" does not exist."
            );
        }

        // Tokenize the files content.
        // If the lexer file, an exception is thrown.
        $lexer = new Lexer($this->getTokenDefinitions());
        $content = file_get_contents($path);
        $content = UTF16Decoder::decode($content);

        // $result is either an array of tokens or false.
        $result = $lexer->tokenize($content);

        // Output some information if tokenizing failed
        if ($result == false) {
            $error = $lexer->getError();
            $this->output->writeln("<error>Failed to parse file \"" . $path  . "\"</error>");
            $this->output->writeln("Line: " . $error['line'] . " Offset: " . $error['offset']);
            $this->output->writeln($error['description']);
        }

        return $result;
    }

    /**
     * Returns the token definitions used to parse the files.
     */
    public function getTokenDefinitions()
    {
        return array(
            new CommentToken(),
            new TranslationEntryToken(),
        );
    }
}

