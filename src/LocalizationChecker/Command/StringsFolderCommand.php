<?php

namespace LocalizationChecker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
    * An extension of the StringsCommand. 
    *
    * This command takes folders instead of files, checks if all the files are there. 
    * And finally it calls for each tuple of files the StringsCommand.
 */
class StringsFolderCommand extends Command
{
    /**
     * An associative array for each folder that was supplied. In that array
     * the relative paths to the files are listed.
     */
    protected $folders;

    public function configure()
    {
        $this->setName('check:strings-folder');
        $this->setDescription('Check folders for .strings files and validate them.');
        $this->addArgument(
            'folders',
            InputArgument::IS_ARRAY,
            'The folders you want to check. The first folder is taken as the reference.'
        );

        $this->configureHelp();
    }

    protected function configureHelp()
    {
        $this->setHelp(<<<EOF
The <info>%command.name%</info> validates folders containig .strings files using the 
check:strings command.

Example:

    <info>php %command.full_name% en.lproj</info>

This will validate the syntax of the localizable.strings files in the folder. If you supply 
multiple folders, these folders will be checked for completness. Example:

    <info>php %command.full_name% en.lproj fr.lproj</info>

Both folders will be checked for completeness. Then all files will be checked for a correct 
syntax. When that check finishes successfully, it will also check if for each key in 
en.lproj/localizable.strings that the according key exists in fr.lproj/localizable.strings by 
using the check:strings command.


EOF
        );
    }

    ////////////////////////////////////////////////////////////////////////
    // Execution
    ////////////////////////////////////////////////////////////////////////

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $folders = $input->getArgument('folders');
        if (!$folders) {
            $output->writeln("<error>No folders to check</error>");
            return -1;
        }

        // Parse all folders
        
    }

    /**
     * Parse the array of folders and fill the 
     */
    protected function parseFolders($folders)
    {
        
    }
    
}
