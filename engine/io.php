<?php
namespace NextFW\Engine;

/**
 * Tool to manage files
 *
 * @author  Bobak
 * @package io
 * @subpackage io
 */
class IO
{
    /**
     * Clean a filename
     *
     * @param string $filename Filename
     * @return string
     */
    public static function clean($filename)
    {
        //Replaces \ by /
        $Out = str_replace('\\', DS, $filename);

        //Replaces /. / By /
        $Out = str_replace('/./', DS, $Out);

        //Corrects .. /
        $OutA = explode(DS, $Out);
        $Nbi = count($OutA);
        for ($i = 0; $i < $Nbi; $i++) {
            if ($OutA[$i] == '..') {
                $OutA[$i] = '';
                if ($i > 0) {
                    $OutA[$i - 1] = '';
                }
            }
        }
        if ($OutA) {
            $Out = implode(DS, $OutA);
        }

        //Removes duplicate /
        $Out = ereg_replace('/{1,}', DS, $Out);

        //Removes bad characters
        $Out = eregi_replace('[^a-z0-9._:/-]', '_', $Out);

        //Protects all characters dangerous for the SHELL
        //$Out=escapeshellcmd($Out);

        //Removes empty
        $Out = str_replace(' ', "_", $Out);

        return $Out;
    }

    /**
     * Check if file exists and back its true path
     *
     * @param string $filename Filename
     * @return string
     */
    public static function exist($filename)
    {
        if (file_exists($filename) AND $filename) {
            return realpath($filename);
        } else {
            return false;
        }
    }

    /**
     * List of files or directories (such as shell)
     *
     * @param string $Nom : *.*
     * @param bool $RepOnly : FALSE=File , TRUE=Directory
     * @return array
     */
    public static function dir($Nom = "*.*", $RepOnly = false)
    {
        $Info = pathinfo($Nom);

        //Dir filtration
        $Rep = self::info($Nom, 'Dir');
        $Filtre = '^' . $Info['basename'] . '$';
        $Filtre = str_replace('.', '\\.', $Filtre);
        $Filtre = str_replace('*', '.*', $Filtre);
        $Filtre = str_replace('?', '.', $Filtre);
        $Out = array();

        if ($FP = @dir($Rep . '.')) {
            while ($Fichier = $FP->read()) {
                if (ereg($Filtre, $Fichier)) {
                    if ($RepOnly) {
                        //Dirs and no . or ..
                        if (is_dir($Rep . $Fichier) and ($Fichier != ".") and ($Fichier != "..")) {
                            $Out[] = $Fichier;
                        }
                    } else {
                        //Files and no MAC Icon
                        if (is_file($Rep . $Fichier) and ($Fichier != "Icon?")) {
                            $Out[] = $Fichier;
                        }
                    }
                }
            }
            @$FP->close();
        }

        return $Out;
    }

    /**
     * Returns information about a file
     *
     * @param string $Fichier Filename
     * @param string $Commande cmd (Full|Dir|File|Ext|Base)
     * @return string
     */
    public static function info($Fichier, $Commande = 'Full')
    {
        //Clean all
        $Fichier = self::clean($Fichier);

        //Split
        $Info = pathinfo($Fichier);

        //Fixes empty directory
        if ($Info['dirname'] == '.') {
            $Info['dirname'] = '';
        } else {
            $Info['dirname'] .= '/';
        }

        //CMD
        $Commande = strtolower($Commande);
        switch ($Commande) {
            case "full":
                $Out = $Fichier;
                break;
            case "dir" :
                $Out = $Info['dirname'];
                break;
            case "file":
                $Out = $Info['basename'];
                break;
            case "ext" :
                $Out = $Info['extension'];
                break;
            case "base":
                $Out = $Info['filename'];
                break;
            default    :
                $Out = false;
        }

        return $Out;
    }

    /**
     * Make a multi directory
     *
     * @param string $Nom Directoy name
     * @return bool
     */
    public static function mkDir($Nom)
    {
        if (self::Exist($Nom)) {
            $Out = true;
        } else {
            $Out = mkdir($Nom, 0755, true);
        }

        return $Out;
    }

    /**
     * Remove a directory with all are contained
     *
     * @param string $Nom Directory name
     * @return bool
     */
    public static function rmDir($Nom)
    {
        if (!self::Exist($Nom)) {
            $Out = false;
        } else {
            //Removes sub directory
            $Reps = self::Dir($Nom . '/*', true);
            $Nb = count($Reps);
            for ($i = 0; $i < $Nb; $i++) {
                self::RmDir($Nom . '/' . $Reps[$i]);
            }

            //Removes file is in directory
            $Fichiers = self::Dir($Nom . '/*', false);
            $Nb = count($Fichiers);
            for ($i = 0; $i < $Nb; $i++) {
                unlink($Nom . '/' . $Fichiers[$i]);
            }

            //end of remove
            $Out = @rmdir($Nom);
        }

        return $Out;
    }

    /**
     * Remove all files that match a search
     *
     * @param string $Nom Filename to match
     * @return bool
     */
    public static function del($Nom)
    {
//Infos
        $Dir = self::Info($Nom, 'Dir');
        $Fichiers = self::Dir($Nom, false);

        $Nb = count($Fichiers);
        if ($Nb) {
            $Out = true;
        } else {
            $Out = false;
        }

//Remove
        for ($i = 0; $i < $Nb; $i++) {
            $Out = @unlink($Dir . '/' . $Fichiers[$i]);
        }

        return $Out;
    }

    /**
     * Sync 2 directorys
     *
     * @param string $RepS input directory
     * @param string $RepC output directory
     * @return bool
     */
    public static function rsync($RepS, $RepC)
    {
        $Out = true;

//Input exist ?
        if (!self::Exist($RepS)) {
            $Out = false;
        } else {
            $Out &= mkdir($RepC);

            //Copy sub directorys
            $Reps = self::Dir($RepS . '/*', true);
            $Nb = count($Reps);
            for ($i = 0; $i < $Nb; $i++) {
                $Out &= self::Rsync($RepS . '/' . $Reps[$i], $RepC . '/' . $Reps[$i]);
            }

            //Copy files in the directory
            $Fichiers = self::Dir($RepS . '/*', false);
            $Nb = count($Fichiers);
            for ($i = 0; $i < $Nb; $i++) {
                $Out &= copy($RepS . '/' . $Fichiers[$i], $RepC . '/' . $Fichiers[$i]);
            }
        }

        return $Out;
    }

    /**
     * Save a array in file
     *
     * @param array /string $Data Data to save
     * @param string $Fichier Filename
     * @param string $EOL End of line
     * @param bool $Append Append mode
     * @return bool
     */
    public static function fileSave($Data, $filename, $Append = false,$EOL = "\n")
    {
        $Out = false;
        //Opening file mode
        if ($Append) {
            $Mode = 'a';
        } else {
            $Mode = 'w';
        }

        //Opening in write mode file
        if ($FId = fopen($filename, $Mode)) {
            //Merge of array if possible
            if (is_array($Data)) {
                $Data = implode($EOL, $Data);
            }

            //Save
            fputs($FId, $Data);
            $Out = fclose($FId);
        }

        return $Out;
    }

    /**
     * Drop a line in a file
     *
     * @param string $Line To drop line
     * @param string $Fichier filename
     * @param string $EOL End of line
     * @param bool $IsEreg Ereg mode
     * @return bool
     */
    public static function fileDelLine($Line, $Fichier, $EOL = "\n", $IsEreg = false)
    {
        $Out = false;

        if ($Data = @file($Fichier)) {
            $Nb = count($Data);
            $UPdate = false;

            if ($IsEreg) {
                //Search with ereg
                for ($i = 0; $i < $Nb; $i++) {
                    if (eregi($Line, $Data[$i])) {
                        unset($Data[$i]);
                        $UPdate = true;
                    }
                }
            } else {
                //Searching for Equality accurate
                $Line .= $EOL;
                for ($i = 0; $i < $Nb; $i++) {
                    if ($Data[$i] == $Line) {
                        unset($Data[$i]);
                        $UPdate = true;
                    }
                }
            }

            //The file has changed, thus saving
            if ($UPdate) {
                self::FileSave($Data, $Fichier, '');
                $Out = true;
            }
        }

        return $Out;
    }
}

class IOException extends \Exception {}
