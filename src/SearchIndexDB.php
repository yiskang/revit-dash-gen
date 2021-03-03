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

use Exception;
use Webmozart\PathUtil\Path;

/**
 * Index DB Helper Class.
 */
class SearchIndexDB
{
    /**
     * The full filename of the  DB file.
     * 
     * @var string
     */
    private $filename;

    /**
     * The SQLite3 DB instance.
     * 
     * @var \SQLite3
     */
    private $db;

    /**
     * True if the DB is opened.
     * @var bool
     */
    private $isOpened = false;

    /**
     * Initializes the instance.
     * 
     * @param string $filename The full filename of the  DB file.
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->isOpened = false;
    }

    /**
     * Open the SQLite DB file.
     * 
     * @throws \Exception Throws an Exception in case of errors.
     */
    public function open()
    {
        if($this->isOpened)
            return;

        $this->db = new \SQLite3($this->filename);
        if(!$this->db)
            throw new Exception($this->db->lastErrorMsg(), $this->db->lastErrorCode);
        
        $this->isOpened = true;
    }

    /**
     * Initializes the package
     * 
     * @throws \Exception Throws an Exception in case of errors.
     */
    public function initialize()
    {
        if(!$this->isOpened)
            $this->open();
        
        $result = $this->db->exec('CREATE TABLE searchIndex(id INTEGER PRIMARY KEY, name TEXT, type TEXT, path TEXT);');
        if(!$result)
            throw new Exception($this->db->lastErrorMsg(), $this->db->lastErrorCode);

        $result = $this->db->exec('CREATE UNIQUE INDEX anchor ON searchIndex (name, type, path);');
        if(!$result)
            throw new Exception($this->db->lastErrorMsg(), $this->db->lastErrorCode);

        $this->close();
    }

    /**
     * Inset new page index into the SQLite DB file.
     * 
     * @param string $name The page name.
     * @param string $type The page type.
     * @param string $path The page path.
     * @param bool [$closeAfterInset=false] True to close the DB after inserting new index.
     * 
     * @throws \Exception Throws an Exception in case of errors.
     */
    public function insertIndex($name, $type, $path, $closeAfterInset = false)
    {
        if(!$this->isOpened)
            $this->open();

        $cmd = $this->db->prepare('INSERT OR IGNORE INTO searchIndex(name, type, path) VALUES (?, ?, ?)');

        $cmd->bindParam(1, $docName);
        $cmd->bindParam(2, $docType);
        $cmd->bindParam(3, $docPath);

        $docName = $name;
        $docType = $type;
        $docPath = $path;
        $result = $cmd->execute();

        if(!$result)
            throw new Exception($this->db->lastErrorMsg(), $this->db->lastErrorCode);

        if($closeAfterInset)
            $this->close();
    }

    /**
     * Close the SQLite DB file.
     */
    public function close()
    {
        if(!$this->isOpened)
            return;

        $this->db->close();
        $this->isOpened = false;
    }
}

?>