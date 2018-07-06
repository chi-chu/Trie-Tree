<?php
/**
 * @desc    : PHP Trie Tree
 * @require : PHP version >= 5.6
 * @author  : luyue
 * @mail    : 544625106@qq.com
 * @date    : 2018-07-05
 */

class TrieTree{
    protected $tree = [];
 
    /**
     * @desc    : You may use this function to load you source word
     */
    public function __construct(){

    }

    /**
     * @desc    : Add a sensetive word to tree
     * @param   : $word [string]
     * @return  : bool
     */
    public function add($word){
        $wordArr = self::stringToArr($word, true);
        $count = count($wordArr);
        $trie = &$this->tree;
        $i = 0;
        while($i < $count){
            if(!array_key_exists($wordArr[$i], $trie)){
                $trie[$wordArr[$i]] = [];
            }
            $trie = &$trie[$wordArr[$i]];
            $i++;
        }
        return true;
    }
 
    /**
     * @desc    : Remove a sensetive word from Trie tree
     * @param   : $word [string]
     * @return  : bool
     */
    public function remove($word){
        $wordArr = self::stringToArr($word, false);
        if($this->find($word)){
            $count = count($wordArr);
            $trie = &$this->tree;
            $i = 0;
            $matchNumArr = [0];
            while($i < $count){
                if(array_key_exists(null, $trie[$wordArr[$i]])){
                    $matchNumArr[] = $i;
                }
                $trie = &$trie[$wordArr[$i]];
                $i++;
            }
            $index = 0;
            $trie = &$this->tree;
            $preTrie = &$this->tree;
            $j = array_slice($matchNumArr, -1, 1)[0];
            $q = array_slice($matchNumArr, -2, 1)[0];
            while ($index <= $j) {
                $trie = &$trie[$wordArr[$index]];
                if($q != 0 && $index <= $q){
                    $preTrie = &$preTrie[$wordArr[$index]];
                }
                $index++;
            }
            if(count($trie) == 1){
                if($q == 0){
                    unset($this->tree[$wordArr[0]]);
                }else{
                    unset($preTrie[$wordArr[$index-1]]);
                }
            }else{
                unset($trie['']);
            }
        }
        return true;
    }
    
    /**
     * @desc    : Find a sensetive word in Trie tree
     * @param   : $word [string]
     * @return  : bool
     */
    public function find($word){
        $wordArr = self::stringToArr($word, true);
        $count = count($wordArr);
        $trie = &$this->tree;
        $i = 0;
        while($i < $count){
            if(!array_key_exists($wordArr[$i], $trie)){
                return false;
            }
            $trie = &$trie[$wordArr[$i]];
            $i++;
        }
        return true;
    }
 
    /**
     * @desc    : check the content whether or not contain any sensetive word
     * @param   : $content [string]
     * @return  : bool
     */
    public function check_string($content){
        if(empty($content)) return false;
        $contentArr = self::stringToArr($content, true);
        $len = count($contentArr);
        $i = 0;
        while($i < $len){
            $tree = &$this->tree;
            $j = $i;
            if(array_key_exists($contentArr[$i], $tree)){
                while($j < $len){
                    if(array_key_exists(null, $tree)){
                        return true;
                    }
                    if(!array_key_exists($contentArr[$j], $tree)){
                        break;
                    }
                    $tree = &$tree[$contentArr[$j]];
                    $j++;
                }
            }
            $i++;
        }
        return false;
    }
    
    /**
     * @desc    : filter the content
     * @param   : $content [string]
     * @return  : array('content'=> filter_content, 'word'=> array('sensetive_word'=> Number))
     */
    public function filter_string($content){
        if(empty($content)) return false;
        $contentArr = self::stringToArr($content, true);
        $len = count($contentArr);
        $i = 0;
        $filterContent = '';
        $word = [];
        while($i < $len){
            $tree = &$this->tree;
            $matchFlag = 0;
            $j = $i;
            while($j < $len && array_key_exists($contentArr[$j], $tree)){
                if(array_key_exists(null, $tree[$contentArr[$j]])){
                    $matchFlag = $j;
                }
                $tree = &$tree[$contentArr[$j]];
                $j++;
            }
            if($matchFlag == 0){
                $filterContent .= $contentArr[$i];
                $i++;
            }else{
                $sensetiveWord = implode('', array_slice($contentArr, $i, $matchFlag-$i+1));
                $filterContent .= '****';
                if(isset($word[$sensetiveWord])){
                    $word[$sensetiveWord] = $word[$sensetiveWord] + 1;
                }else{
                    $word[$sensetiveWord] = 1;
                }
                $i = $matchFlag + 1;
            }
        }
        return ['content'=>$filterContent, 'word'=>$word];
    }

    /**
     * @desc    : Change string to array
     * @param   : $s [string]
     * @return  : array
     */
    public static function stringToArr($s, $addNull){
        $s = trim($s);
        $len = strlen($s);
        if($len == 0) return [];
        $sArr = [];
        for($i = 0;$i < $len;$i++){
            $n = ord($s[$i]);
            if(($n >> 7) == 0){           //0xxx xxxx, asci, single
                $sArr[] = $s[$i];
            }else if(($n >> 4) == 15){    //1111 xxxx, first in four char
                if($i < $len - 3){
                    $sArr[] = $s[$i].$s[$i + 1].$s[$i + 2].$s[$i + 3];
                    $i += 3;
                }
            }else if(($n >> 5) == 7){     //111x xxxx, first in three char
                if($i < $len - 2){
                    $sArr[] = $s[$i].$s[$i + 1].$s[$i + 2];
                    $i += 2;
                }
            }else if(($n >> 6) == 3){     //11xx xxxx, first in two char
                if($i < $len - 1){
                    $sArr[] = $s[$i].$s[$i + 1];
                    $i++;
                }
            }
        }
        if($addNull){
            $sArr[] = null;
        }
        return $sArr;
    }
 
    /**
     * @desc    : Export the tree to compressed data
     * @return  : bin string
     */
    public function exportTree(){
        return gzcompress(json_encode($this->tree));
    }
 
    /**
     * @desc    : Load the compressed tree data
     * @param   : $treeStr [string]
     * @return  : null
     */
    public function importTree($treeStr){
        $this->tree = json_decode(gzuncompress($treeStr), true);
    }

}

//Here is thre example: you may add your own word source
$trie_obj = new TrieTree();
$filtermsg = 'here is the content that you want to filter the sensetive word like fuck fucked fucker';
$a = microtime(true);
$list = explode("\r\n", file_get_contents(dirname(__file__).'/word.txt'));
foreach ($list as $value) {
	$trie_obj->add($value);
}
$b = microtime(true);
$data = $trie_obj->filter_string($filtermsg);
$c = microtime(true);
echo '敏感词数量：  '.count($list)."\r\n";
echo '文章总长度：  '.mb_strlen($filtermsg)."\r\n";
echo '字典构建耗时：'.($b-$a)."\r\n";
echo '检索耗时：    '.($c-$b)."\r\n";
print_r($data);
