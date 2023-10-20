<?php
declare(strict_types=1);

namespace Test\Unit\Directory\WriteTo;

use Test\Cases\FileSystemCase;
use Test\Cases\IoEnvCase;
use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class WriteToBindedIntoExistingWithSetLocalNameTest extends IoEnvCase
{
    protected $outDirectoryName = 'WriteToBindedIntoExistingWithSetLocalNameTest';

    public function testWriteTo()
    {
        $this->createFilesystem([
            'directories' => [
                $rootPath = $this->outPath . '/root',
                $child1Path = $this->outPath . '/child1',
                $child2Path = $this->outPath . '/child2',
                $child3Path = $this->outPath . '/child3',
                $child4Path = $this->outPath . '/child4',
                $child5Path = $this->outPath . '/child5',
                $childLPath = $this->outPath . '/childL',
            ],
            'files' => [
                $file1Path = $this->outPath . '/file1.txt' => 'initial1',
                $file7Path = $this->outPath . '/file7.txt' => 'initial7',
                $file6Path = $this->outPath . '/file6.txt' => 'initial6',
                $file5Path = $this->outPath . '/file5.txt' => 'initial5',
                $file4Path = $this->outPath . '/file4.txt' => 'initial4',
                $file3Path = $this->outPath . '/file3.txt' => 'initial3',
                $file2Path = $this->outPath . '/file2.txt' => 'initial2',
            ],
        ]);
        $outPath = $this->outPath;


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

        $file1->setContent('file1.txt');
        $file2->setContent('file2.txt');
        $file3->setContent('file3.txt');
        $file4->setContent('file4.txt');
        $file5->setContent('file5.txt');
        $file6->setContent('file6.txt');
        $file7->setContent('file7.txt');

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

        $this->createDirectory($outPath . '/childL/root');
        $this->createFile($outPath . '/childL/file1.txt', 'existing file1.txt');
        $this->createFile($outPath . '/childL/root/file7.txt', 'existing file7.txt');

        $childL->setLocalName('renamedChild');
        $file3->setLocalName('file3renamed.txt');
        $childL->writeTo($outPath);

        self::assertDirectoryExists($outPath . '/renamedChild/root/child1/child2/child3/child4/child5');
        self::assertFileExists($file1PathControl = $outPath . '/renamedChild/file1.txt');
        self::assertFileExists($file7PathControl = $outPath . '/renamedChild/root/file7.txt');
        self::assertFileExists($file6PathControl = $outPath . '/renamedChild/root/child1/file6.txt');
        self::assertFileExists($file5PathControl = $outPath . '/renamedChild/root/child1/child2/file5.txt');
        self::assertFileExists($file4PathControl = $outPath . '/renamedChild/root/child1/child2/child3/file4.txt');
        self::assertFileExists($file3PathControl = $outPath . '/renamedChild/root/child1/child2/child3/child4/file3renamed.txt');
        self::assertFileExists($file2PathControl = $outPath . '/renamedChild/root/child1/child2/child3/child4/child5/file2.txt');

        self::assertEquals('file1.txt', file_get_contents($file1PathControl));
        self::assertEquals('file2.txt', file_get_contents($file2PathControl));
        self::assertEquals('file3.txt', file_get_contents($file3PathControl));
        self::assertEquals('file4.txt', file_get_contents($file4PathControl));
        self::assertEquals('file5.txt', file_get_contents($file5PathControl));
        self::assertEquals('file6.txt', file_get_contents($file6PathControl));
        self::assertEquals('file7.txt', file_get_contents($file7PathControl));
    }

}
