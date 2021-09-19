<?php

namespace Test\Unit\Directory;

use Test\Cases\FileSystemCase;
use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class DirectoryWorkPathsTest extends FileSystemCase
{
    private $outPath;

    public function setUp(): void
    {
        $this->createDirectory($this->outPath = $this->getRuntimePath() .  '/DirectoryWorkPathsTest');
    }

    public function testGetPathsAllDirectories()
    {
        $this->createDirectory($rootPath1 = $this->outPath . '/root');
        $this->createDirectory($childPath1 = $this->outPath . '/child1');
        $this->createDirectory($childPath2 = $this->outPath . '/child1/child2');
        $this->createDirectory($childPath3 = $this->outPath . '/child3');
        $this->createDirectory($childPath4 = $this->outPath . '/child3/child4');
        $this->createDirectory($childPath5 = $this->outPath . '/child3/child4/child5');
        $this->createDirectory($childPathL = $this->outPath . '/child3/child4/childL');
        $root = Directory::createBinded($rootPath1);
        $child1 = Directory::createBinded($childPath1);
        $child2 = Directory::createBinded($childPath2);
        $child3 = Directory::createBinded($childPath3);
        $child4 = Directory::createBinded($childPath4);
        $child5 = Directory::createBinded($childPath5);
        $childL = Directory::createBinded($childPathL);
        $root->addDirectory($child1);
        $root->addDirectory($child2);
        $root->addDirectory($child3);
        $root->addDirectory($child4);
        $root->addDirectory($child5);
        $root->addDirectory($childL);

        $allPaths = $root->getPathsAllDirectories();

        self::assertContains($rootPath1, $allPaths);
        self::assertContains($childPath1, $allPaths);
        self::assertContains($childPath2, $allPaths);
        self::assertContains($childPath3, $allPaths);
        self::assertContains($childPath4, $allPaths);
        self::assertContains($childPath5, $allPaths);
        self::assertContains($childPathL, $allPaths);
        self::assertCount(7, $allPaths);
    }

    public function testGetPathsAllDirectoriesNested()
    {
        $this->createDirectory($rootPath1 = $this->outPath . '/root');
        $this->createDirectory($childPath1 = $this->outPath . '/child1');
        $this->createDirectory($childPath2 = $this->outPath . '/child1/child2');

        $this->createDirectory($childPath3 = $this->outPath . '/child3');
        $this->createDirectory($childPath4 = $this->outPath . '/child3/child4');
        $this->createDirectory($childPath5 = $this->outPath . '/child3/child4/child5');
        $this->createDirectory($childPathL = $this->outPath . '/child3/child4/childL');

        $root = Directory::createBinded($rootPath1);
        $child1 = Directory::createBinded($childPath1);
        $child2 = Directory::createBinded($childPath2);
        $child3 = Directory::createBinded($childPath3);
        $child4 = Directory::createBinded($childPath4);
        $child5 = Directory::createBinded($childPath5);
        $childL = Directory::createBinded($childPathL);

        $root->addDirectory($child1);
        $child1->addDirectory($child2);

        $root->addDirectory($child3);
        $child3->addDirectory($child4);
        $child4->addDirectory($child5);
        $child4->addDirectory($childL);

        $allPaths = $root->getPathsAllDirectories();

        self::assertContains($rootPath1, $allPaths);
        self::assertContains($childPath1, $allPaths);
        self::assertContains($childPath2, $allPaths);
        self::assertContains($childPath3, $allPaths);
        self::assertContains($childPath4, $allPaths);
        self::assertContains($childPath5, $allPaths);
        self::assertContains($childPathL, $allPaths);
        self::assertCount(7, $allPaths);


        $child1AllPaths = $child1->getPathsAllDirectories();

        self::assertContains($childPath1, $child1AllPaths);
        self::assertContains($childPath2, $child1AllPaths);
        self::assertCount(2, $child1AllPaths);

        $child3AllPaths = $child3->getPathsAllDirectories();

        self::assertContains($childPath3, $child3AllPaths);
        self::assertContains($childPath4, $child3AllPaths);
        self::assertContains($childPath5, $child3AllPaths);
        self::assertContains($childPathL, $child3AllPaths);
        self::assertCount(4, $child3AllPaths);

        $child4AllPaths = $child4->getPathsAllDirectories();

        self::assertContains($childPath4, $child4AllPaths);
        self::assertContains($childPath5, $child4AllPaths);
        self::assertContains($childPathL, $child4AllPaths);
        self::assertCount(3, $child4AllPaths);


        $childLAllPaths = $childL->getPathsAllDirectories();

        self::assertContains($childPathL, $childLAllPaths);
        self::assertCount(1, $childLAllPaths);
    }

    public function testGetPathsAllDirectoriesNestedChaos()
    {
        $this->createDirectory($rootPath1 = $this->outPath . '/root');
        $this->createDirectory($childPath1 = $this->outPath . '/child1');
        $this->createDirectory($childPath2 = $this->outPath . '/child1/child2');
        $this->createDirectory($childPath3 = $this->outPath . '/child3');
        $this->createDirectory($childPath4 = $this->outPath . '/child3/child4');
        $this->createDirectory($childPath5 = $this->outPath . '/child3/child4/child5');
        $this->createDirectory($childPathL = $this->outPath . '/child3/child4/childL');
        $root = Directory::createBinded($rootPath1);
        $child1 = Directory::createBinded($childPath1);
        $child2 = Directory::createBinded($childPath2);
        $child3 = Directory::createBinded($childPath3);
        $child4 = Directory::createBinded($childPath4);
        $child5 = Directory::createBinded($childPath5);
        $childL = Directory::createBinded($childPathL);

        $root->addDirectory($child1);   // /root/child1
        $root->addDirectory($child3);   // /root/child3
        $child1->addDirectory($child2); // /root/child1/child2
        $child3->addDirectory($child4); // /root/child3/child4
        $child3->addDirectory($child5); // /root/child3/child5
        $child3->addDirectory($childL); // /root/child3/childL
        $child3->addDirectory($child1); // /root/child3/child1
        $child3->addDirectory($child2); // /root/child3/child2

        $child1->addDirectory($child3); // /root/child1/child3
        $child1->addDirectory($root);   // /child1/root
        $root->addDirectory($child1);   // /root/child1
        $child2->addDirectory($root);   // /child2/root

        $allPaths = $child2->getPathsAllDirectories();

        self::assertContains($rootPath1, $allPaths);
        self::assertContains($childPath1, $allPaths);
        self::assertContains($childPath2, $allPaths);
        self::assertContains($childPath3, $allPaths);
        self::assertContains($childPath4, $allPaths);
        self::assertContains($childPath5, $allPaths);
        self::assertContains($childPathL, $allPaths);
        self::assertCount(7, $allPaths);


        $rootAllPaths = $root->getPathsAllDirectories();

        self::assertContains($rootPath1, $rootAllPaths);
        self::assertContains($childPath1, $rootAllPaths);
        self::assertContains($childPath3, $rootAllPaths);
        self::assertContains($childPath4, $rootAllPaths);
        self::assertContains($childPath5, $rootAllPaths);
        self::assertContains($childPathL, $rootAllPaths);
        self::assertCount(6, $rootAllPaths);

        $child1AllPaths = $child1->getPathsAllDirectories();

        self::assertContains($childPath1, $child1AllPaths);
        self::assertContains($childPath3, $child1AllPaths);
        self::assertContains($childPath4, $child1AllPaths);
        self::assertContains($childPath5, $child1AllPaths);
        self::assertContains($childPathL, $child1AllPaths);
        self::assertCount(5, $child1AllPaths);


        $child3AllPaths = $child3->getPathsAllDirectories();

        self::assertContains($childPath3, $child3AllPaths);
        self::assertContains($childPath4, $child3AllPaths);
        self::assertContains($childPath5, $child3AllPaths);
        self::assertContains($childPathL, $child3AllPaths);
        self::assertCount(4, $child3AllPaths);

        $child5AllPaths = $child5->getPathsAllDirectories();

        self::assertContains($childPath5, $child5AllPaths);
        self::assertCount(1, $child5AllPaths);
    }

    public function testGetPathsAllDirectoriesNestedMixedBindedChaos()
    {
        $this->createDirectory($rootPath1 = $this->outPath . '/root');
        $this->createDirectory($childPath1 = $this->outPath . '/child1');
        $this->createDirectory($this->outPath . '/child1/child2');
        $this->createDirectory($childPath3 = $this->outPath . '/child3');
        $this->createDirectory($this->outPath . '/child3/child4');
        $this->createDirectory($childPath5 = $this->outPath . '/child3/child4/child5');
        $this->createDirectory($childPathL = $this->outPath . '/child3/child4/childL');

        $root = Directory::createBinded($rootPath1);
        $child1 = Directory::createBinded($childPath1);
        $child2 = new Directory('child2');
        $child3 = Directory::createBinded($childPath3);
        $child4 = new Directory('child4');
        $child5 = Directory::createBinded($childPath5);
        $childL = Directory::createBinded($childPathL);

        $root->addDirectory($child1);   // /root/child1
        $root->addDirectory($child3);   // /root/child3
        $child1->addDirectory($child2); // /root/child1/child2
        $child3->addDirectory($child4); // /root/child3/child4
        $child3->addDirectory($child5); // /root/child3/child5
        $child3->addDirectory($childL); // /root/child3/childL
        $child3->addDirectory($child1); // /root/child3/child1
        $child3->addDirectory($child2); // /root/child3/child2

        $child1->addDirectory($child3); // /root/child1/child3
        $child1->addDirectory($root);   // /child1/root
        $root->addDirectory($child1);   // /root/child1
        $child2->addDirectory($root);   // /child2/root

        $allPaths = $child2->getPathsAllDirectories();

        self::assertContains($rootPath1, $allPaths);
        self::assertContains($childPath1, $allPaths);
        self::assertContains($childPath3, $allPaths);
        self::assertContains($childPath5, $allPaths);
        self::assertContains($childPathL, $allPaths);
        self::assertCount(5, $allPaths);


        $rootAllPaths = $root->getPathsAllDirectories();

        self::assertContains($rootPath1, $rootAllPaths);
        self::assertContains($childPath1, $rootAllPaths);
        self::assertContains($childPath3, $rootAllPaths);
        self::assertContains($childPath5, $rootAllPaths);
        self::assertContains($childPathL, $rootAllPaths);
        self::assertCount(5, $rootAllPaths);

        $child1AllPaths = $child1->getPathsAllDirectories();

        self::assertContains($childPath1, $child1AllPaths);
        self::assertContains($childPath3, $child1AllPaths);
        self::assertContains($childPath5, $child1AllPaths);
        self::assertContains($childPathL, $child1AllPaths);
        self::assertCount(4, $child1AllPaths);


        $child3AllPaths = $child3->getPathsAllDirectories();

        self::assertContains($childPath3, $child3AllPaths);
        self::assertContains($childPath5, $child3AllPaths);
        self::assertContains($childPathL, $child3AllPaths);
        self::assertCount(3, $child3AllPaths);

        $child5AllPaths = $child5->getPathsAllDirectories();

        self::assertContains($childPath5, $child5AllPaths);
        self::assertCount(1, $child5AllPaths);
    }

    public function testGetPathsAllFiles()
    {
        $this->createDirectory($rootPath1 = $this->outPath . '/root');
        $this->createDirectory($childPath1 = $this->outPath . '/child1');
        $this->createDirectory($childPath2 = $this->outPath . '/child1/child2');
        $this->createDirectory($childPath3 = $this->outPath . '/child3');
        $this->createDirectory($childPath4 = $this->outPath . '/child3/child4');
        $this->createDirectory($childPath5 = $this->outPath . '/child3/child4/child5');
        $this->createDirectory($childPathL = $this->outPath . '/child3/child4/childL');


        $this->createFile($filePath1 = $this->outPath . '/root/file1.txt');
        $this->createFile($filePath2 = $this->outPath . '/child1/file2.txt');
        $this->createFile($filePath3 = $this->outPath . '/child1/child2/file3.txt');
        $this->createFile($filePath4 = $this->outPath . '/child3/file4.txt');
        $this->createFile($filePath5 = $this->outPath . '/child3/child4/file5.txt');
        $this->createFile($filePath6 = $this->outPath . '/child3/child4/child5/file6.txt');
        $this->createFile($filePath7 = $this->outPath . '/child3/child4/childL/file7.txt');


        $root = Directory::createBinded($rootPath1);
        $child1 = Directory::createBinded($childPath1);
        $child2 = Directory::createBinded($childPath2);
        $child3 = Directory::createBinded($childPath3);
        $child4 = Directory::createBinded($childPath4);
        $child5 = Directory::createBinded($childPath5);
        $childL = Directory::createBinded($childPathL);
        $root->addDirectory($child1);
        $root->addDirectory($child2);

        $child1->addDirectory($child3);
        $child1->addDirectory($child4);

        $child2->addDirectory($child5);
        $child5->addDirectory($childL);


        $file1 = File::createBinded($filePath1);
        $file2 = File::createBinded($filePath2);
        $file3 = File::createBinded($filePath3);
        $file4 = File::createBinded($filePath4);
        $file5 = File::createBinded($filePath5);
        $file6 = File::createBinded($filePath6);
        $file7 = File::createBinded($filePath7);

        $root->addFile($file1);
        $child1->addFile($file2);
        $child2->addFile($file3);
        $child3->addFile($file4);
        $child4->addFile($file5);
        $child5->addFile($file6);
        $childL->addFile($file7);

        $allPaths = $root->getPathsAllFiles();

        self::assertContains($filePath1, $allPaths);
        self::assertContains($filePath2, $allPaths);
        self::assertContains($filePath3, $allPaths);
        self::assertContains($filePath4, $allPaths);
        self::assertContains($filePath5, $allPaths);
        self::assertContains($filePath6, $allPaths);
        self::assertContains($filePath7, $allPaths);
        self::assertCount(7, $allPaths);

        $allPathsChild2 = $child2->getPathsAllFiles();
        self::assertContains($filePath3, $allPathsChild2);
        self::assertContains($filePath6, $allPathsChild2);
        self::assertContains($filePath7, $allPathsChild2);
        self::assertCount(3, $allPathsChild2);


        $allPathsChild1 = $child1->getPathsAllFiles();

        self::assertContains($filePath2, $allPathsChild1);
        self::assertContains($filePath4, $allPathsChild1);
        self::assertContains($filePath5, $allPathsChild1);
        self::assertCount(3, $allPathsChild1);

        $allPathsChildL = $childL->getPathsAllFiles();
        self::assertContains($filePath7, $allPathsChildL);
        self::assertCount(1, $allPathsChildL);
    }

    public function testGetPathsAllFilesMixedBinded()
    {
        $this->createDirectory($rootPath1 = $this->outPath . '/root');
        $this->createDirectory($childPath1 = $this->outPath . '/child1');
        $this->createDirectory($childPath2 = $this->outPath . '/child1/child2');
        $this->createDirectory($childPath3 = $this->outPath . '/child3');
        $this->createDirectory($childPath4 = $this->outPath . '/child3/child4');
        $this->createDirectory($childPath5 = $this->outPath . '/child3/child4/child5');
        $this->createDirectory($childPathL = $this->outPath . '/child3/child4/childL');


        $this->createFile($filePath1 = $this->outPath . '/root/file1.txt');
        $this->createFile($filePath2 = $this->outPath . '/child1/file2.txt');
        $this->createFile($filePath6 = $this->outPath . '/child3/child4/child5/file6.txt');
        $this->createFile($filePath7 = $this->outPath . '/child3/child4/childL/file7.txt');


        $root = Directory::createBinded($rootPath1);
        $child1 = Directory::createBinded($childPath1);
        $child2 = Directory::createBinded($childPath2);
        $child3 = Directory::createBinded($childPath3);
        $child4 = Directory::createBinded($childPath4);
        $child5 = Directory::createBinded($childPath5);
        $childL = Directory::createBinded($childPathL);
        $root->addDirectory($child1);
        $root->addDirectory($child2);

        $child1->addDirectory($child3);
        $child1->addDirectory($child4);

        $child2->addDirectory($child5);
        $child5->addDirectory($childL);


        $file1 = File::createBinded($filePath1);
        $file2 = File::createBinded($filePath2);
        $file3 = new File($filePath3 = 'file3.txt');
        $file4 = new File($filePath4 = 'file4.txt');
        $file5 = new  File($filePath5 = 'file5.txt');
        $file6 = File::createBinded($filePath6);
        $file7 = File::createBinded($filePath7);

        $root->addFile($file1);
        $child1->addFile($file2);
        $child2->addFile($file3);
        $child3->addFile($file4);
        $child4->addFile($file5);
        $child5->addFile($file6);
        $childL->addFile($file7);

        $allPaths = $root->getPathsAllFiles();

        self::assertContains($filePath1, $allPaths);
        self::assertContains($filePath2, $allPaths);
        self::assertNotContains($filePath3, $allPaths);
        self::assertNotContains($filePath4, $allPaths);
        self::assertNotContains($filePath5, $allPaths);
        self::assertContains($filePath6, $allPaths);
        self::assertContains($filePath7, $allPaths);
        self::assertCount(4, $allPaths);

        $allPathsChild2 = $child2->getPathsAllFiles();
        self::assertNotContains($filePath3, $allPathsChild2);
        self::assertContains($filePath6, $allPathsChild2);
        self::assertContains($filePath7, $allPathsChild2);
        self::assertCount(2, $allPathsChild2);


        $allPathsChild1 = $child1->getPathsAllFiles();

        self::assertContains($filePath2, $allPathsChild1);
        self::assertNotContains($filePath4, $allPathsChild1);
        self::assertNotContains($filePath5, $allPathsChild1);
        self::assertCount(1, $allPathsChild1);

        $allPathsChildL = $childL->getPathsAllFiles();
        self::assertContains($filePath7, $allPathsChildL);
        self::assertCount(1, $allPathsChildL);
    }

    public function testGetPathsAllFilesMixedBindedChaos()
    {
        $this->createDirectory($rootPath1 = $this->outPath . '/root');
        $this->createDirectory($childPath1 = $this->outPath . '/child1');
        $this->createDirectory($childPath2 = $this->outPath . '/child1/child2');
        $this->createDirectory($childPath3 = $this->outPath . '/child3');
        $this->createDirectory($childPath4 = $this->outPath . '/child3/child4');
        $this->createDirectory($childPath5 = $this->outPath . '/child3/child4/child5');
        $this->createDirectory($childPathL = $this->outPath . '/child3/child4/childL');


        $this->createFile($filePath1 = $this->outPath . '/root/file1.txt');
        $this->createFile($filePath2 = $this->outPath . '/child1/file2.txt');
        $this->createFile($filePath6 = $this->outPath . '/child3/child4/child5/file6.txt');
        $this->createFile($filePath7 = $this->outPath . '/child3/child4/childL/file7.txt');


        $root = Directory::createBinded($rootPath1);
        $child1 = Directory::createBinded($childPath1);
        $child2 = Directory::createBinded($childPath2);
        $child3 = Directory::createBinded($childPath3);
        $child4 = Directory::createBinded($childPath4);
        $child5 = Directory::createBinded($childPath5);
        $childL = Directory::createBinded($childPathL);
        $root->addDirectory($child1);
        $root->addDirectory($child3);
        $child1->addDirectory($child2);
        $child3->addDirectory($child4);
        $child3->addDirectory($child5);
        $child3->addDirectory($childL);
        $child3->addDirectory($child1);
        $child3->addDirectory($child2);


        $file1 = File::createBinded($filePath1);
        $file2 = File::createBinded($filePath2);
        $file3 = new File($filePath3 = 'file3.txt');
        $file4 = new File($filePath4 = 'file4.txt');
        $file5 = new  File($filePath5 = 'file5.txt');
        $file6 = File::createBinded($filePath6);
        $file7 = File::createBinded($filePath7);

        $root->addFile($file1);
        $child1->addFile($file2);
        $child2->addFile($file3);
        $child3->addFile($file4);
        $child4->addFile($file5);
        $child5->addFile($file6);
        $childL->addFile($file7);
        $child1->addFile($file7);
        $childL->addFile($file2);
        $child3->addFile($file6);

        $child1->addDirectory($child3);
        $child1->addDirectory($root);
        $root->addDirectory($child1);
        $child2->addDirectory($root);

        $allPaths = $child2->getPathsAllFiles();

        self::assertContains($filePath1, $allPaths);
        self::assertContains($filePath2, $allPaths);
        self::assertNotContains($filePath3, $allPaths);
        self::assertNotContains($filePath4, $allPaths);
        self::assertNotContains($filePath5, $allPaths);
        self::assertContains($filePath6, $allPaths);
        self::assertContains($filePath7, $allPaths);

        self::assertCount(4, $allPaths);

        //root child1 child3 \ f2 f7 f1 f6
        $allPathsChild2 = $root->getPathsAllFiles();
        self::assertContains($filePath1, $allPathsChild2);
        self::assertContains($filePath2, $allPathsChild2);
        self::assertContains($filePath6, $allPathsChild2);
        self::assertContains($filePath7, $allPathsChild2);

        self::assertNotContains($filePath3, $allPathsChild2);
        self::assertNotContains($filePath4, $allPathsChild2);
        self::assertNotContains($filePath5, $allPathsChild2);

        self::assertCount(4, $allPathsChild2);
        // child1 child3 \ f2 f7 f6
        $allPathsChild1 = $child1->getPathsAllFiles();
        self::assertContains($filePath2, $allPathsChild1);
        self::assertContains($filePath6, $allPathsChild1);
        self::assertContains($filePath7, $allPathsChild1);

        self::assertNotContains($filePath1, $allPathsChild1);
        self::assertNotContains($filePath3, $allPathsChild1);
        self::assertNotContains($filePath4, $allPathsChild1);
        self::assertNotContains($filePath5, $allPathsChild1);

        self::assertCount(3, $allPathsChild1);
        // childL \ f2
        $allPathsChildL = $childL->getPathsAllFiles();
        self::assertContains($filePath2, $allPathsChildL);

        self::assertNotContains($filePath1, $allPathsChildL);
        self::assertNotContains($filePath3, $allPathsChildL);
        self::assertNotContains($filePath4, $allPathsChildL);
        self::assertNotContains($filePath5, $allPathsChildL);
        self::assertNotContains($filePath6, $allPathsChildL);
        self::assertNotContains($filePath7, $allPathsChildL);

        self::assertCount(1, $allPathsChildL);
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
        $this->delete($this->outPath);
    }
}
