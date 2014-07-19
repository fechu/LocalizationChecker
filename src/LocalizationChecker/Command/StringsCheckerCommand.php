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

/**
 * Class StringsCheckerCommand
 * @author Sandro Meier
 */
class StringsCheckerCommand extends Command
{
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
        $files = $files = $input->getArgument('files');
        if ($files) {
            // Parse all files
            foreach($files as $file) {
                $tokens = $this->parseFile($file);
                var_dump($tokens);
            }
        }
        else {
            $output->writeln("<error>No files to check</error>");
        }
    }

    /**
     * Parses the .strings file at the given path.
     *
     * @param string    $path   The path to the file that should be parsed. If the file does
     *                          not exist, an InvalidArgumentException is thrown.
     * 
     * @return The array of tokens if the lexer could sucessfully tokenize the file.
     *
     * @throws InvalidArgumentException If the lexer failes to tokenize the file's content, or if
     *                                  the file at the given path does not exist.
     */
    protected function parseFile($path)
    {
        $fs = new Filesystem();

        // Check if the given file exists
        if(!$fs->exists($path)) {
            throw new \InvalidArgumentException(
                "File at path \"" . $path . "\" does not exist."
            );
        }

        // Tokenize the files content.
        // If the lexer file, an exception is thrown.
        $lexer = new Lexer($this->getTokenDefinitions());
        $content = file_get_contents($path);
        $content = UTF16Decoder::decode($content);
        $tokens = $lexer->tokenize($content);

        return $tokens;
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

