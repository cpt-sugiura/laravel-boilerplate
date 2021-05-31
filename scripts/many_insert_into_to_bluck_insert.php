<?php

function replaceSql(string $srcFilePath)
{
    $srcFp = fopen($srcFilePath, 'rb');
    $distFp = fopen(
        preg_replace('#(.*)(\.[^.]*)#', '$1_bulk_inserts$2', $srcFilePath),
        'wb'
    );

    $insertSentence = null;
    while ($line = fread($srcFp, 2**16)){
        if($insertSentence === null){
            preg_match('#(INSERT INTO.*VALUES)#', $line, $matches);
            if(isset($matches[0]) && !empty($matches[0])){
                $insertSentence = $matches[0];
                fwrite($distFp, $insertSentence. "\n");
            }
        }
        $distContent = preg_replace(
                ['/INSERT INTO.*VALUES/', '/\);/'],
                ['', '),'],
                $line
            );
        fwrite($distFp, $distContent);
    }
    fseek($distFp,ftell($distFp) - 1);
    fwrite($distFp, ';');
    fclose($srcFp);
    fclose($distFp);
}

function main()
{
    foreach(
        glob('D:\PhpstormProjects\_CPOINTLAB\新しいフォルダー\_MYSQL_STORE\*.sql')
        as
        $path
    ){
        echo $path."\n";
        replaceSql($path);
    }
}

main();
