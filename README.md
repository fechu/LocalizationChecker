# Localization Checker

iOS & Mac projects localization checker. 

This tool does the following checks for you:

- Scans localized folders (e.g. `en.lproj`) and checks if all of them contain the same files. 
    This helps you detecting missing translation files. 
- Tokenizes `.strings` files and compares with the files of the other translations. This test
    will detect missing translation keys.

## Usage

Basic command to check multiple (can be more than 2) **files**:

    php checker.php check:strings en.lproj/localizable.strings de.lproj/localizable.strings

The syntax of the files will be validated. And afterwards it is checked if all files contain the 
keys of `en.proj/localizable.strings`. The first file in the list will always be taken as the
reference. I suggest you to supply the "original" language as the first argument.

Basic command to check multiple **folders** with `.strings` files:

    php checker.php check:strings-folder en.lproj/ de.lproj/

All `.strings` files in the folder are searched. Then the tool will check if in all files are 
present in all folders. Again the first folder is taken as the reference. when that check was 
successful the tool will also check all the files using `check:strings` command to validate syntax
and check if all keys are there.

## Todo

- Add Screenshot to README
- Add options to disable specific tests

### Further tests to add

- Check for empty values
- Check for keys whichs value is equal to the key (not yet translated)
- Compare number of placeholders
- Check for duplicated keys

## Known Issues

- `check:strings-folder` command works only if all folders have trailing slashes.
- `check:strings` does not work with multiline translation values


## Development
The following section describe things of the development process.
### ctag Support

The repository contains a file called `generate-ctags.sh`. This will generate 2 ctag files (`tags` and `vendor.tags`), if you have `exurbetant-ctags` installed. 
These files help some editors with autocompletion, especially vim.
