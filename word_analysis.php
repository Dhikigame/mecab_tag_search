<?php

    require_once 'sub_tag_search.php';
    require_once 'mecab/mecab_dic.php';


    /*
        以下の記事にあるRSSの[title]と[dc:subject]を解析
        記事：http://blog.livedoor.jp/nanjstu/archives/54211788.html
        RSS：http://blog.livedoor.jp/nanjstu/index.rdf
    */
    $title_query = "西武が優勝したのは阪神と中日のせいという風潮wwwuwwwuww";
    $maintag_query = "埼玉西武ライオンズ";
    $sports_num = 1;//1:野球関連、2:サッカー関連、3:バスケ関連
    
    //情報入力(タイトル：記事のタイトル、メインタグ：RSS内の記事に登録されているタグ、カテゴリ：何のスポーツ関連のブログか数値で指定)
    echo "【記事情報】<br>";
    echo "タイトル：".$title_query."<br>";
    echo "メインタグ：".$maintag_query."<br>";
    echo "カテゴリ：";
    if($sports_num == 1){
        echo "野球<br>";
    }else if($sports_num == 2){
        echo "サッカー<br>";
    }else{
        echo "バスケ<br>";
    }

    //配列のnull値をなくし、新しい配列に登録する
    function tag_sort($tags){
        $tags_count = 0;
        $new_tags_count = 0;
        $new_tags = array(null,null,null,null,null,null,null,null,null,null,null,null);//新しい配列：$tagsに登録されているタグを登録

        for($tags_count = 0;$tags_count < 12;$tags_count++){
            if($tags[$tags_count] != null){
                $new_tags[$new_tags_count] = $tags[$tags_count];
                $new_tags_count++;
            }
        }
        return $new_tags;
    }

    /*
        使うMecabの辞書を$optionに登録する
        あらかじめ、php内でMecab使える設定と使う辞書のパス指定の必要がある(GitHub:https://github.com/neologd/mecab-ipadic-neologd)
    */
    $options = mecab_dic();

	$mecab = new \MeCab\Tagger($options);//辞書をTaggerに登録
    $tags = array(null,null,null,null,null,null,null,null,null,null,null,null);//登録したタグ
    $word_tag = array(null,null,null,null,null,null,null,null,null,null,null,null);//sub_tag_search関数で出力した結果

	/*	メインタグ解析	*/
	$nodes = $mecab->parseToNode($maintag_query);//メインタグの文字列を分割
    $word_count = 0;//登録したタグの総数
    echo "【解析結果】<br>";
	foreach($nodes as $node){
        $word = $node->getSurface();//形態素の取得
        $word_tag = sub_tag_search($word, $sports_num); //$word_tag[0-11] 0-11:解析したタグ情報格納
        //$tags(登録タグ)にsub_tag_searchで解析したタグを登録する　タグの総数は$word_countに格納
        $i = 0;
		while($word_tag[$i] != null){
            $tags[$word_count] = $word_tag[$i];
            echo "tag:".$tags[$word_count];
            $word_count++;
            $i++;
            if($word_tag[$word_count] == null){
                echo "<br>";
            }
        }

        //タグを$tags配列に挿入した場合、重複があればタグを取り除く
        if($word_tag[0] != null){
            $word_count_tmp = $word_count - 1;//$word_count_tmp:word_countのタグ格納数を保存(ループで使用)
                for($count_index = 0;$count_index <= $word_count_tmp;$count_index++){
                    for($count = 0;$count <= $word_count_tmp;$count++){
                        if(($tags[$count_index] == $tags[$count]) && ($tags[$count] != null) && ($count_index != $count)){
                            $word_count--;
                            $tags[$count] = null;
                        }
                    }
                }
        }
        
        //配列のnull値をなくし、新しい配列に登録する
		if($word_count != 0){
			$tags = tag_sort($tags);
        }
	}

	/*	 タイトル解析	*/
	$nodes = $mecab->parseToNode($title_query);//タイトルの文字列を分割
	foreach($nodes as $node){
        $word = $node->getSurface();//形態素の取得
        $word_tag = sub_tag_search($word, $sports_num); //$word_tag[0-11] 0-11:解析したタグ情報格納
        
        //$tags(登録タグ)にsub_tag_searchで解析したタグを登録する　タグの総数は$word_countに格納
        $i = 0;
		while($word_tag[$i] != null){
            $tags[$word_count] = $word_tag[$i];
            echo "tag:".$tags[$word_count];
            $word_count++;
            $i++;
            if($word_tag[$i] == null){
                echo "<br>";
            }
        }

        //タグを$tags配列に挿入した場合、重複があればタグを取り除く
        if($word_tag[0] != null){
            $word_count_tmp = $word_count - 1;//$word_count_tmp:word_countのタグ格納数を保存(ループで使用)
                for($count_index = 0;$count_index <= $word_count_tmp;$count_index++){
                    for($count = 0;$count <= $word_count_tmp;$count++){
                        if(($tags[$count_index] == $tags[$count]) && ($tags[$count] != null) && ($count_index != $count)){
                            $word_count--;
                            $tags[$count] = null;
                        }
                    }
                }
        }
        
        //配列のnull値をなくし、新しい配列に登録する
		if($word_count != 0){
			$tags = tag_sort($tags);
        }
	}

    
    //タグが12個埋まらない場合はNoneを挿入
    $word_count = 0;
	while($word_count <= 11){
		if($tags[$word_count] == null){
			$tags[$word_count] = "None";
		}
		$word_count++;
    }

    //登録しているタグを出力
    $word_count = 0;
    echo "【登録タグ】<br>";
    while($word_count < 12){
        echo "タグ".($word_count + 1)."：".$tags[$word_count]."<br>";
        $word_count++;
    }
?>