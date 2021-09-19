<?php

namespace Test\Unit\Directory\Copy;

use Test\Cases\IoEnvCase;
use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;

class DirectoryIoEnvBindedFilesystemWithSetLocalNameTest extends IoEnvCase
{
    protected $outDirectoryName = 'DirectoryIoEnvBindedFilesystemWithSetLocalNameTest';

    public function testCopy()
    {
        $this->createFilesystem();
        $outPath = $this->outPath;
        $this->createDirectory($outPath . '/copy1');
        $this->createDirectory($outPath . '/copy2');
        $this->createDirectory($outPath . '/copy3');

        $childLPath = $this->outPath . '/childL';
        $rootPath = $this->outPath . '/childL/root';
        $child1Path = $this->outPath . '/childL/root/child1';
        $child2Path = $this->outPath . '/childL/root/child1/child2';
        $child3Path = $this->outPath . '/childL/root/child1/child2/child3';
        $child4Path = $this->outPath . '/childL/root/child1/child2/child3/child4';
        $child5Path = $this->outPath . '/childL/root/child1/child2/child3/child4/child5';


        $file1Path = $this->outPath . '/childL/file1.txt';
        $file7Path = $this->outPath . '/childL/root/file7.txt';
        $file6Path = $this->outPath . '/childL/root/child1/file6.txt';
        $file5Path = $this->outPath . '/childL/root/child1/child2/file5.txt';
        $file4Path = $this->outPath . '/childL/root/child1/child2/child3/file4.txt';
        $file3Path = $this->outPath . '/childL/root/child1/child2/child3/child4/file3.txt';
        $file2Path = $this->outPath . '/childL/root/child1/child2/child3/child4/child5/file2.txt';


        $root =  Directory::createBinded($rootPath);
        $child1 =  Directory::createBinded($child1Path);
        $child2 =  Directory::createBinded($child2Path);
        $child3 =  Directory::createBinded($child3Path);
        $child4 =  Directory::createBinded($child4Path);
        $child5 =  Directory::createBinded($child5Path);
        $childL =  Directory::createBinded($childLPath);

        $file1 =  File::createBinded($file1Path);
        $file2 =  File::createBinded($file2Path);
        $file3 =  File::createBinded($file3Path);
        $file4 =  File::createBinded($file4Path);
        $file5 =  File::createBinded($file5Path);
        $file6 =  File::createBinded($file6Path);
        $file7 =  File::createBinded($file7Path);

        //fresh mix root #1
        $root->addDirectory($child1)->addDirectory($child2)->addDirectory($child3);

        /**
         * result fresh mix root #1
         * root
         * |___child1
         *     |___child2
         *         |___child3
         */

        //fresh mix root #2
        $root->addDirectory($child4)->addDirectory($child5)->addDirectory($childL);

        /**
         * result fresh mix root #2
         * root
         * |___child1
         * |   |___child2
         * |       |___child3
         * |
         * |___child4
         *     |___child5
         *        |___childL
         */

        //fresh mix files #1
        $root->addFile($file1);
        $child1->addFile($file2);
        $child2->addFile($file3);
        $child3->addFile($file4);
        $child4->addFile($file5);
        $child5->addFile($file6);
        $childL->addFile($file7);
        /**
         * result fresh mix files #1
         * root
         * |___child1
         * |   |___child2
         * |   |   |___child3
         * |   |   |   |___file4.txt
         * |   |   |___file3.txt
         * |   |___file2.txt
         * |
         * |___child4
         * |   |___child5
         * |   |   |___childL
         * |   |   |   |____file7.txt
         * |   |   |____file6.txt
         * |   |___file5.txt
         * |
         * |___file1.txt
         */

        //fresh mix files #2
        $root->addFile($file7);
        $child1->addFile($file6);
        $child2->addFile($file5);
        // $child3->addFile($file4); //Already Exist
        $child4->addFile($file3);
        $child5->addFile($file2);
        $childL->addFile($file1);

        /**
         * result fresh mix files #2
         * root
         * |___child1
         * |   |___child2
         * |   |   |___child3
         * |   |   |   |___file4.txt
         * |   |   |___file5.txt
         * |   |___file6.txt
         * |
         * |___child4
         * |   |___child5
         * |   |   |___childL
         * |   |   |   |____file1.txt
         * |   |   |____file2.txt
         * |   |___file3.txt
         * |___file7.txt
         */

        $root->copy($outPath . '/copy1');

        self::assertDirectoryExists($outPath . '/copy1/root/child1/child2/child3');
        self::assertDirectoryExists($outPath . '/copy1/root/child4/child5/childL');

        self::assertFileExists($outPath . '/copy1/root/file7.txt');
        self::assertFileExists($outPath . '/copy1/root/child1/file6.txt');
        self::assertFileExists($outPath . '/copy1/root/child1/child2/file5.txt');
        self::assertFileExists($outPath . '/copy1/root/child1/child2/child3/file4.txt');

        self::assertFileExists($outPath . '/copy1/root/child4/file3.txt');
        self::assertFileExists($outPath . '/copy1/root/child4/child5/file2.txt');
        self::assertFileExists($outPath . '/copy1/root/child4/child5/childL/file1.txt');

        //fresh mix directory #1
        $childL->addDirectory($root);
        /**
         * result fresh mix directory #1
         * childL
         * |___root
         * |   |___child1
         * |   |   |___child2
         * |   |   |   |___child3
         * |   |   |   |   |___file4.txt
         * |   |   |   |___file5.txt
         * |   |   |___file6.txt
         * |   |
         * |   |___child4
         * |   |   |___child5
         * |   |   |   |___file2.txt
         * |   |   |___file3.txt
         * |   |___file7.txt
         * |
         * |___file1.txt
         */
        $childL->copy($outPath . '/copy2');

        self::assertDirectoryExists($outPath . '/copy2/childL/root/child1/child2/child3');
        self::assertDirectoryExists($outPath . '/copy2/childL/root/child4/child5');
        self::assertFileExists($outPath . '/copy2/childL/file1.txt');
        self::assertFileExists($outPath . '/copy2/childL/root/file7.txt');
        self::assertFileExists($outPath . '/copy2/childL/root/child1/file6.txt');
        self::assertFileExists($outPath . '/copy2/childL/root/child1/child2/file5.txt');
        self::assertFileExists($outPath . '/copy2/childL/root/child1/child2/child3/file4.txt');
        self::assertFileExists($outPath . '/copy2/childL/root/child4/file3.txt');
        self::assertFileExists($outPath . '/copy2/childL/root/child4/child5/file2.txt');

        //fresh mix directory #2
        $child3->addDirectory($child4);
        /**
         * result fresh mix directory #2
         * childL
         * |___root
         * |   |___child1
         * |   |   |___child2
         * |   |   |   |___child3
         * |   |   |   |   |___child4
         * |   |   |   |   |   |___child5
         * |   |   |   |   |   |   |___file2.txt
         * |   |   |   |   |   |___file3.txt
         * |   |   |   |   |___file4.txt
         * |   |   |   |___file5.txt
         * |   |   |___file6.txt
         * |   |___file7.txt
         * |___file1.txt
         */
        $childL->setLocalName('renamedChild');
        $file3->setLocalName('file3renamed.txt');
        $childL->copy($outPath . '/copy3');

        self::assertDirectoryExists($outPath . '/copy3/renamedChild/root/child1/child2/child3/child4/child5');
        self::assertFileExists($outPath . '/copy3/renamedChild/file1.txt');
        self::assertFileExists($outPath . '/copy3/renamedChild/root/file7.txt');
        self::assertFileExists($outPath . '/copy3/renamedChild/root/child1/file6.txt');
        self::assertFileExists($outPath . '/copy3/renamedChild/root/child1/child2/file5.txt');
        self::assertFileExists($outPath . '/copy3/renamedChild/root/child1/child2/child3/file4.txt');
        self::assertFileExists($outPath . '/copy3/renamedChild/root/child1/child2/child3/child4/file3renamed.txt');
        self::assertFileExists($outPath . '/copy3/renamedChild/root/child1/child2/child3/child4/child5/file2.txt');
    }

}
