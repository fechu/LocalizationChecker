<?php

namespace LocalizationChecker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \SplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Input\ArrayInput;


/**
    * An extension of the StringsCommand. 
    *
    * This command takes folders instead of files, checks if all the files are there. 
    * And finally it calls for each tuple of files the StringsCommand.
 */
class StringsFolderCommand extends Command
{
    /**
     * An associative array for each folder that was supplied. The objects associated with 
     * each folder are SplFileInfo objects. To get the filename you can use getRelativePathname().
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

        // Run the StringsCommand on all files if there haven't been any errors so far.
        if ($errorCount == 0) {
            $errorCount = $this->checkStrings();
        }

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

            foreach ($finder as $fileObject){
                $this->folders[$folderPath][] = $fileObject;
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////
    // Checks
    ////////////////////////////////////////////////////////////////////////

    /**
     * Checks if all files in the base folder ($baseFolder) are also present in the
     * other folders. 
     */
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
                        $filename = $file->getRelativePathname();
                        $this->output->writeln(
                            '<error>File "'. $filename .'" missing in folder "'. $folder .'"</error>'
                        );
                    }
                }
            }
        }

        return $errorCount;
    }
    
    /**
     * Checks all the strings files using the StringsCommand. 
     *
     * Run this command only if run checkFolderAgainstBaseFolder sucessfully. Otherwhise
     * there may be unexpected behaviour. 
     *
     * This method will run the StringsCommand on all pairs of files in the folders. E.g. all 
     * files that have the same relative path. 
     */
    protected function checkStrings()
    {
        // We expect that all files in the base folder are available in all other folders. 
        // This means, we can just use the base folder to get the relative path names and prepend
        // the folder names. Let's do it!

        $errorCount = 0;
        $baseFolderFiles = $this->folders[$this->baseFolder];

        foreach ($baseFolderFiles as $file) {
            // Prepare the arguments
            $filesArgument = array();
            foreach ($this->folders as $folder => $files){
                $filesArgument[] = $folder . $file->getRelativePathname();
            }

            $arguments = array(
                "files" => $filesArgument,
            );

            // Start the StringsCommand
            $command = $this->getApplication()->find("check:strings");
            $input = new ArrayInput($arguments);
            $errorCount += $command->run($input, $this->output); 
        }
            
        return $errorCount;
    }
    

}
