<?php

/////////////////////////////////////////////////////////////////////
// Copyright (c) Autodesk, Inc. All rights reserved
// Written by Forge Partner Development
//
// Permission to use, copy, modify, and distribute this software in
// object code form for any purpose and without fee is hereby granted,
// provided that the above copyright notice appears in all copies and
// that both that copyright notice and the limited warranty and
// restricted rights notice below appear in all supporting
// documentation.
//
// AUTODESK PROVIDES THIS PROGRAM "AS IS" AND WITH ALL FAULTS.
// AUTODESK SPECIFICALLY DISCLAIMS ANY IMPLIED WARRANTY OF
// MERCHANTABILITY OR FITNESS FOR A PARTICULAR USE.  AUTODESK, INC.
// DOES NOT WARRANT THAT THE OPERATION OF THE PROGRAM WILL BE
// UNINTERRUPTED OR ERROR FREE.
/////////////////////////////////////////////////////////////////////

    namespace Autodesk\ADN;

    require_once __DIR__ . '/vendor/autoload.php';

    use CHMLib\CHM;
    use Webmozart\PathUtil\Path;

    $arguments = Util::arguments($argv);

    print_r($arguments);

    $chmFile = $arguments['input'][1];
    $docBundleName = $arguments['name'];
    $docBundleIdentifier = $arguments['id'];
    $outputDir = (Path::getExtension($arguments['out'])) ? Path::getDirectory($arguments['out']) : $arguments['out'];
    $docBundleFilename = (Path::getExtension($arguments['out'])) ? Path::getFilename($arguments['out']) : null;

    //\var_dump($chmFile, $outputDir, $docBundleName, $docBundleIdentifier, $docBundleFilename);

    $conf = new Configuration($docBundleIdentifier, $docBundleName);
    $docSet = new DocSet($conf);

    $basePath = $outputDir;
    $docSet->initialize($basePath, $chmFile);
    $docSet->extract();
    $docSet->finalize();

    if($docBundleFilename !== null)
        $docSet->rename($docBundleFilename);

?>