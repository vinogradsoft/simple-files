<?php

namespace Test\Unit\Directory\Delete;

use Test\Cases\AdditionalAssertCase;
use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;

class DirectoryAdditionalAssertWithSetLocalNameTest extends AdditionalAssertCase
{
    protected $outDirectoryName = 'DirectoryAdditionalAssertWithSetLocalNameTest';

    public function testDelete()
    {
        $this->createFilesystem([
            'directories' => [
                $childLPath = $this->outPath . '/childL',
                $rootPath = $this->outPath . '/childL/root',
                $child1Path = $this->outPath . '/childL/root/child1',
                $child2Path = $this->outPath . '/childL/root/child1/child2',
                $child3Path = $this->outPath . '/childL/root/child1/child2/child3',
                $child4Path = $this->outPath . '/childL/root/child1/child2/child3/child4',
            ],
            'files' => [
                $file1Path = $this->outPath . '/childL/file1.txt' => 'initial1',
                $file7Path = $this->outPath . '/childL/root/file7.txt' => 'initial7',
                $file6Path = $this->outPath . '/childL/root/child1/file6.txt' => 'initial6',
                $file3Path = $this->outPath . '/childL/root/child1/child2/child3/child4/file3.txt' => 'initial3',
            ],
        ]);

        $outPath = $this->outPath;
        $this->createDirectory($outPath . '/move');

        $root = Directory::createBinded($rootPath);
        $child1 = Directory::createBinded($child1Path);
        $child2 = Directory::createBinded($child2Path);
        $child3 = Directory::createBinded($child3Path);
        $child4 = Directory::createBinded($child4Path);
        $child5 = new Directory('child5');
        $childL = Directory::createBinded($childLPath);

        $file1 = File::createBinded($file1Path);
        $file2 = new File('file2.txt');
        $file3 = File::createBinded($file3Path);
        $file4 = new File('file4.txt');
        $file5 = new File('file5.txt');
        $file6 = File::createBinded($file6Path);
        $file7 = File::createBinded($file7Path);

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
        $childL->setLocalName('renamedChild');
        $file3->setLocalName('file3renamed.txt');
        $childL->delete();


        $this->assertEmptyObjectDirectory($childL);
        $this->assertEmptyObjectDirectory($root);
        $this->assertEmptyObjectDirectory($child1);
        $this->assertEmptyObjectDirectory($child2);
        $this->assertEmptyObjectDirectory($child3);
        $this->assertEmptyObjectDirectory($child4);
        $this->assertEmptyObjectDirectory($child5);

        $this->assertEmptyObjectFile($file1);
        $this->assertEmptyObjectFile($file2);
        $this->assertEmptyObjectFile($file3);
        $this->assertEmptyObjectFile($file4);
        $this->assertEmptyObjectFile($file5);
        $this->assertEmptyObjectFile($file6);
        $this->assertEmptyObjectFile($file7);

        self::assertDirectoryNotExists($childLPath);
        self::assertDirectoryNotExists($rootPath);
        self::assertDirectoryNotExists($child1Path);
        self::assertDirectoryNotExists($child2Path);
        self::assertDirectoryNotExists($child3Path);
        self::assertDirectoryNotExists($child4Path);

        self::assertFileNotExists($file1Path);
        self::assertFileNotExists($file7Path);
        self::assertFileNotExists($file6Path);
        self::assertFileNotExists($file3Path);
    }

}
