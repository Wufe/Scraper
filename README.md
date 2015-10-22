# Generic Scraper

## This is a scraper powered by PHP & composer

# The purpose

For now, the main purpose is to locate the main content of a web page


To start:
```shell
php src/index.php
```


# How it works

After the download of the page, there are 4 steps:

## Parsing

In this phase there's the generation of the tree structure.

## Advanced parsing

There's an augmentation of info carried by the tree.

## Scan

The scan acts looking for patterns.

## Tweak

The tweak can be ad-hoc for each site, but in this case is only one and it's generic.
Its main purpose is to choose a threshold for the identification of the results.

#

The ( raw & messy ) results are stored ( for now ) in a file called "priority", after the execution of the code