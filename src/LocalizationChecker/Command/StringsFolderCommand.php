<?php

namespace LocalizationChecker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \SplFileInfo;
use Symfony\Component\Finder\Finder;


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

    /**
     * The path to the folder which should be used as a reference.
     */
    protected $baseFolder;

    /**
     * Reference to the output interface objects. 
     *
     * This way not only the execute() method can produce output.
     */
    protected $output;

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
        // Store a reference to the output object
        $this->output = $output;

        $folders = $input->getArgument('folders');
        if (!$folders) {
            $output->writeln("<error>No folders to check</error>");
            return -1;
        }

        // Check if all of these supplied paths are real folders
        foreach ($folders as $folderPath){
            $folderInfo = new SplFileInfo($folderPath);
            if(!$folderInfo->isDir()) {
                // We got a non folder as an argument.
                $output->writeln('<error>Object at path "' . $folderPath . '" is either not a folder or does not exist.');
                return -1;
            }
        }

        // Store a reference to the base folder
        // The base folder is the first one that is supplied.
        $this->baseFolder = $folders[0];

        // Parse all folders
        $this->parseFolders($folders);

        // Check if all files in the baseFolder exist in the other folders.
        $errorCount = $this->checkFoldersAgainstBaseFolder();

        return $errorCount;
    }

    /**
     * Parse the array of folders and fill the $folders property.
     */
    protected function parseFolders($folders)
    {
        foreach ($folders as $folderPath){

            // Prepare the array in the files array.
            $this->folders[$folderPath] = array();

            // Find all the *.strings files
            $finder = new Finder();
            $finder
                ->files()
                ->name("*.strings")
                ->in($folderPath);

            foreach ($finder as $filePath){
                $this->folders[$folderPath][] = $filePath;
            }
        }
    }

    protected function checkFoldersAgainstBaseFolder()
    {
        // Initialization
        $errorCount = 0;

        // Precalculations
        $filesToCheck = $this->folders[$this->baseFolder];
        $filesToCheckCount = count($filesToCheck);

        foreach ($this->folders as $folder => $files){
            // Check if they are equal with the filesToCheck array
            $intersectionCount = array_intersect($filesToCheck, $files);

            if ($intersectionCount != $filesToCheckCount) {
                // There are some files missing. Do a closer investigation
                foreach ($filesToCheck as $file) {
                    if (!in_array($file, $files)) {
                        $errorCount++;
                        $this->output->writeln(
                            '<error>File "'. $file .'" missing in folder "'. $folder .'"</error>'
                        );
                    }
                }
            }
        }

        return $errorCount;
    }
    

}
