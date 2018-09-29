<?php

    /*
        使うMecabの辞書を$optionsに登録する
        あらかじめ、php内でMecab使える設定と使う辞書のパス指定の必要がある(使用したMecabの辞書　GitHub:https://github.com/neologd/mecab-ipadic-neologd)
    */
function mecab_dic(){
    $options = ['-d', '/usr/local/mecab/lib/mecab/dic/mecab-ipadic-neologd/'];

    return $options;
}

?>