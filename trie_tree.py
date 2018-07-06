# -*- coding: utf-8 -*-
import time
import os

class TrieTree(object):
    """
    Trie Tree by Python
    well,  you may use Python lib Pickle to write the tree to bin file
    and load the file instead of create the tree every time
    
    import pickle
    
    """
    __author__ = 'Luyue'
    __authorMail__   = '544625106@qq.com'

    def __init__(self):
        self.trie = {}
       
    def add(self, word):
        #Add a sensetive word
        p = self.trie
        word = word.strip()
        for c in word:
            if not c in p:
                p[c] = {}
            p = p[c]
        if word != '':
            p[''] = ''
        return True
    
    def remove(self, word):
        #Remove a sensetive word ,return bool
        if self.search(word):
            slen = len(word)
            tempTrie = self.trie
            index = 0
            mark = [0]
            while index < slen:
                if '' in tempTrie[word[index]]:
                    mark.append(index)
                tempTrie = tempTrie[word[index]]
                index = index + 1
            if tempTrie == {'':''}:
                if mark[-2] == 0:
                    del(self.trie[word[0]])
                else:
                    eval('self.trie'+''.join(["['%s']" % x for x in word[:mark[-2]+1]])).pop(word[mark[-2]+1])
            else:
                eval('self.trie'+''.join(["['%s']" % x for x in word[:mark[-1]+1]])).pop('')
        return True

    def search(self, word):
        #Check the Trie Tree Whecher has the sensetive word, return bool
        p = self.trie
        word = word.lstrip()
        for c in word:
            if not c in p:
                return False
            p = p[c]
        if '' in p:
            return True
        return False
    
    def check_string(self, string):
        #Check the content whether has any sensetive word , return bool
        if self.trie == {}:
            return False
        iLen = len(string)
        i = 0
        while i < iLen:
            trie = self.trie
            j = i
            while (j < iLen and string[j] in trie):
                if '' in trie[string[j]]:
                    return True
                trie = trie[string[j]]
                j = j + 1
            i = i + 1
        return False

    def filter_string(self, string):
        #Filter the content, return the string and the list of sensetive word
        retstring = ''
        filterWord = dict()
        if self.trie == {}:
            return string
        iLen = len(string.lstrip())
        i = 0
        while i < iLen:
            trie = self.trie
            MaxMatchNum = 0
            j = i
            while (j< iLen and string[j] in trie):
                if '' in trie[string[j]]:
                    MaxMatchNum = j
                trie = trie[string[j]]
                j = j + 1
            if MaxMatchNum == 0:
                retstring += string[i]
                i = i + 1
            else:
                retstring += u'*' * (MaxMatchNum-i+1)
                sensetiveWord = string[i:MaxMatchNum+1]
                if sensetiveWord in filterWord:
                    filterWord[sensetiveWord] = filterWord[sensetiveWord] + 1
                else:
                    filterWord[sensetiveWord] = 1
                i = MaxMatchNum + 1
        return retstring, filterWord
    
if __name__ == '__main__':
    trie_obj = TrieTree()
    filtermsg = 'here is the content that you want to filter the sensetive word like fuck fucked fucker';
    a = time.time()
    #Change the code to your local sensetive word source and Add word
    fp = open(os.path.dirname(__file__) + '/word.txt' ,'r',  encoding='UTF-8')
    words = []
    wordnum = 0;
    for line in fp:
        wordnum = wordnum + 1
        trie_obj.add(line.strip("\n"))
    fp.close()
    b = time.time()
    msg, filterWord = trie_obj.filter_string(filtermsg)
    c = time.time()
    print('敏感词数量：  '+ str(wordnum))
    print('文章总长度：  '+ str(len(filtermsg)))
    print('字典构建耗时：'+ str(b-a))
    print('检索耗时：    '+ str(c-b))
    print(msg)
    print(filterWord)
 