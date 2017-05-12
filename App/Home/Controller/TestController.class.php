<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/3/1
 * Time: 17:22
 */

namespace Home\Controller;

define('N', 1000);        //总数
define('P', rand(1,N));   //开始报数的位置
define('M', rand(1,N/2)); //报数的间距\

class TestController
{


    public function change(){
        set_time_limit(0);
        $data = M("addr_info_20090430")->where(array('mobile_7'=>array('like','182%')))
            ->field('mobile_7,status,no,addr_2')->select();
        foreach($data as $val){
            M("mobile")->add(array('no'=>$val['no'],'addr'=>$val['addr_2'],
                'head'=>$val['mobile_7'],
                'mobile'=>$val['mobile_7'].'2951','status'=>$val['status']));
        }
        echo '完成！';
    }
    public function getsbname(){
        set_time_limit(0);
        $data = M("addr_info_20090430")->where(array('mobile_7'=>array('like','182%')))
            ->getField('mobile_7',true);
//        $url = "https://shenghuo.alipay.com/home/queryUserStatus.json?timeSnap=1488879071127
//        &ua=227n%2BqZ9mgNqgJnCG0Yus65w7XCvsi%2FyLDVddU%3D%7CnOiH84T%2FhP%2BF%2BYr9h%2FuI8lI%3D%7CneiHGXz1WexE7V%2Fkge6L84fijehy3m%2FIesB2E7MT%7Cmu6b9JE%2BsxmtKl4prNZc8FshsBWHCLI1uDG9Jo81RuBZxWHHTyqK%7Cm%2B%2BT%2FGIWeQ15DX4RZglsB2wfpMFhwQ%3D%3D%7CmOOM6Yws%7CmeWK74oq%7CluKW%2BWcCqR6pGqzRo9FnzGTVf9Rs3q8LpAi7H6PSdMZ2BLIaqwx%2BzWnVZc18y7kQvBO8zWbRYQRrDmvLaw%3D%3D%7Cl%2BGOEGQRZgl6FWEXYBxzB3QNdBtvGWAZdgx1DmEVYxpjDHYAe9t7%7ClO6BH3ofcARrHmYTsxM%3D%7CleGS%2FZgGcQh9C3cYh%2BKW4Zvsl%2B%2BY653hlu6aJVYnXyhQKFssVSZfJVAqUSRcJbrfsMRkAaE%3D%7CkuqFG37SYdamELgcqQ%2Bk1HrSbgtk%2BoPwi%2BSR5pw8UyBPKk8gVCFZL48v%7Ck%2BmGGH3RYtWlE7sfqgyn13nRbQhnE3wIfQZ%2B3n4%3D%7CkOeIFnPfbNurHbURpAKp2XffYwZpE2cSfQh%2FCmURaRpgwGA%3D%7CkeaJF3IXeAx%2FBH8QZh5iDXoGcwSkBA%3D%3D%7CjvmWCG0IZx1nEn0IcgdoEGYQZMRk%7Cj%2FeYBmPPfMu7DaUBtBK5yWfPcxZ5557kkf6L%2FI8vQDNcOVwzSjlBOZk5%7CjPuUCm8KZRlgFnkMewhnHGYabMxs%7CjfqVC24LZBFkEX4JfQlmEmEdahGxEQ%3D%3D%7Civ2SDGkMYxtiGnUAegBvG24daxOzEw%3D%3D%7Ci%2FOcAmfLeM%2B%2FCaEFsBa9zWPLdxJ945vglfqP9oIiTT5RNFE%2BSj9KPETkRA%3D%3D%7CiPCfAWTIe8y8CqIGsxW%2BzmDIdBF%2B4JjknfKH8IwsQzBfOl8wRDFJP0XlRQ%3D%3D%7CifCfAWTIe8y8CqIGsxW%2BzmDIdBF%2BAngXZAt%2FCX8Me9t7%7Chv%2BQDmvHdMOzBa0JvBqxwW%2FHex5xBXYCbR5xBXMIcAmpCQ%3D%3D%7Ch%2F6RD2rGdcKyBKwIvRuwwG7Geh9wDHYZagVxBnUDetp6%7ChP2SDGnFdsGxB68Lvhizw23FeRxzB3QBbh1yBnEHcAurCw%3D%3D%7ChfyTDWjEd8CwBq4KvxmywmzEeB1yBnUAbxxzB3AIew%2BvDw%3D%3D%7CgvuUCm%2FDcMe3AakNuB61xWvDfxp1CXAfbAN3AHoCetp6%7Cg%2FqVC27Ccca2AKgMuR%2B0xGrCfht0AHMEaxh3A3sIfQSkBA%3D%3D%7CgPmWCG3BcsW1A6sPuhy3x2nBfRh3C3cYawRwCH4EfNx8%7CgfiXCWzAc8S0AqoOux22xmjAfBl2AnEHaBt0AHgBdgWlBQ%3D%3D%7CvseoNlP%2FTPuLPZUxhCKJ%2BVf%2FQyZJMl0uQTVOOkw6mjo%3D%7Cv8apN1L%2BTfqKPJQwhSOI%2BFb%2BQidIM1wvQDRPOkI6mjo%3D%7CvMWqNFH9TvmJP5czhiCL%2B1X9QSRLP0w6VSZJPUE1Rj2dPQ%3D%3D%7CvcSrNVD8T%2FiIPpYyhyGK%2BlT8QCVKNkolVjlNMUYwSOhI%7CusOsMlf7SP%2BPOZE1gCaN%2FVP7RyJNOUo8UyBPO0c9SDubOw%3D%3D%7Cu8KtM1b6Sf6OOJA0gSeM%2FFL6RiNMMEskVzhMMEw7R%2BdH%7CuM%2BgPls%2BUSRdKEcxRjpVIFMpUCaGJg%3D%3D%7Cuc6hP1o%2FUClQJ0g9RjNcKVwlXCeHJw%3D%3D%7Cts6hP1o%2FUM60zL%2FQpd6kBGsYdxJ3GG0YZB1qymo%3D%7Ct82iPFn1RvGBN587jiiD8131SSxDMF8qXC9cK4sr%7CtMOww6zfsMWq3qndssS8xqnRrcK4wK%2Fbr9i3w6zbqMe7yKfTpMu9x6jescSr37DMo9OnyLjNotKky7Pcq8Swx6jcqsWxxKvfq8Sww6zQv8Sr0b7HqNC%2FyKfRvsuk0L%2FMo9Om0r3NuM2i0qbRvsJi&rdsToken=lJgqD14B7Gih7NbOLwDJFIXfw0cvYacE
//        &userId=&_input_charset=utf-8&r=1488879071128&ctoken=-eMN9hJw953ZC8if&_callback=arale.cache.callbacks.jsonp2";
        $account = '15166087372';
//        foreach($data as $val){
//            $account = $val.'2951';
//        }
        $url = "https://shenghuo.alipay.com/home/queryUserStatus.json?timeSnap=1488880507059&ua=027n%2BqZ9mgNqgJnCG0Yus65w7XCvsi%2FyLDVddU%3D%7CnOiH84T%2FhP%2BE94D7jPeB91c%3D%7CneiHGXz1WexE7V%2Fkge6L84fijehy3m%2FIesB2E7MT%7Cmu6b9JE%2Flgqh2nzRdcmwN447kwGUErY9k%2Bpn%2FmUToga%2BGYAJjehI%7Cm%2B%2BT%2FGIWeQ15DnkWYhh3EmIOYwi%2BCmQKZgq0ErYAqMOozW3N%7CmOOM6Yws%7CmeWK74oq%7CluKW%2BWcCqR6pGqzRo9FnzGTVf9Rs3q8LpAi7H6PSdMZ2BLIaqwx%2BzWnVZc18y7kQvBO8zWbRYeNB8lXCfs1l5UHmQ%2BhM4UfjReNJ4UznTepO5krhhOuO60vr%7Cl%2BGOEGQRZgl6FWEXYBxzB3QNdBtvGWAZdgx1DmEVYxpjDHYAe9t7%7ClO6BH3ofcARrHGQQsBA%3D%7CleGS%2FZgGcQh9C3cYh%2BKW4Zvsl%2B%2BY653hlu6aJVYnXyhQKFssVSZfJVAqUSRcJbrfsMRkAaE%3D%7CkuWKFHEUewN3Am0bYBZ5D3sHdNR0%7Ck%2BSLFXDcb9ioHrYSpwGq2nTcYAVqEmkSfQh%2FA2wbZxNpyWk%3D%7CkOiHGXzQY9SkEroeqw2m1njQbAlm%2BID8iOeS5Z8%2FUCNMKUwjWy9aL48v%7CkeuEGn%2FTYNenEbkdqA6l1XvTbwplEX4GcgRy0nI%3D%7CjvSbBWDMf8i4DqYCtxG6ymTMcBV6CWYdaBxkxGQ%3D%7Cj%2FWaBGEEaxh3DHkPeNh4%7CjPuUCm8KZRJuFXoPdQxjF2AUYRq6Gg%3D%3D%7CjfWaBGHNfsm5D6cDthC7y2XNcRR75Zzrkf6L%2FIsrRDdYPVg3QzRMNEPjQw%3D%3D%7CivCfAWQBbhp1AXYOdwurCw%3D%3D%7Ci%2FGeAGXJes29C6MHshS%2Fz2HJdRB%2FC2QQZx9kHr4e%7CiPGeAGXJes29C6MHshS%2Fz2HJdRB%2FA3kWZQp%2BBHIOddV1%7CifCfAWTIe8y8CqIGsxW%2BzmDIdBF%2BCnkNYhF%2BCnECeQ2tDQ%3D%3D%7Chv%2BQDmvHdMOzBa0JvBqxwW%2FHex5xDXcYawRwC34HctJy%7Ch%2F6RD2rGdcKyBKwIvRuwwG7Geh9wBHcCbR5xBX4GcASkBA%3D%3D%7ChP2SDGnFdsGxB68Lvhizw23FeRxzB3QBbh1yBn0HcwamBg%3D%3D%7ChfyTDWjEd8CwBq4KvxmywmzEeB1yDncYawRwDHoJfNx8%7CgvuUCm%2FDcMe3AakNuB61xWvDfxp1AXIFahl2An4GcAenBw%3D%3D%7Cg%2FqVC27Ccca2AKgMuR%2B0xGrCfht0AHMFahl2A3AEfwioCA%3D%3D%7CgPmWCG3BcsW1A6sPuhy3x2nBfRh3C3cYawRxAnUDe9t7%7CgfiXCWzAc8S0AqoOux22xmjAfBl2AnEHaBt0AXILcgGhAQ%3D%3D%7CvseoNlP%2FTPuLPZUxhCKJ%2BVf%2FQyZJNU4hUj1IO0c0QOBA%7Cv8inOVz4XvhK8kP0fMd023%2FTYgdoEWsdcgd%2BBGseahBoEbER%7CvMSrNVA1WsS9xrDfqtGpCWYVeh96FWAUaB9kxGQ%3D%7CvceoNlP%2FTPuLPZUxhCKJ%2BVf%2FQyZJOlUgVSZSJoYm%7Cus2%2BzaLRvsuk0KfTvMqyyKffo8y2zqHVoda5zaLVpsm1xqnRvsmm0qfIvsap36jHsceo2KzDudag07zJtdqu1rnNutWh17jMudai1rnNvtGtwrnWrMO61a3Cssa%2F0KDbtMC4GA%3D%3D&rdsToken=kfYh8cjay6DvreORCaHk6JVX3naudVFA&account=$account&userId=&_input_charset=utf-8&r=1488880507059&ctoken=h1GdwSvd3L9vfVVZ&_callback=arale.cache.callbacks.jsonp2";
        "https://shenghuo.alipay.com/home/queryUserStatus.json?timeSnap=1488939588619&ua=105n%2BqZ9mgNqgJnCG0Yus65w7XCvsi%2FyLDVddU%3D%7CnOiH84T%2FhPiO8orwh%2F%2BE8FA%3D%7CneiHGXz1WexE7V%2Fkge6L84fijehy3m%2FIesB2E7MT%7Cmu6b9JEMmAW%2BLag5lBNnE4T8dvBF0UbUYBqjK5sRlzytH7ICiO1N%7Cm%2B%2BT%2FGIUewN6AG8Veh%2BjCr0zgjSf%2Blr6%7CmOOM6Yws%7CmeWK74oq%7CluKW%2BWcCqR6pGqzRo9FnzGTVf9Rs3q8LpAi7H6PSdMZ2BLIaqwx%2BzWnVZc18y7kQvBO8zWbRYeNB8lXCfs1l5UHmQ%2BhM4UfjReNJ4UznTepO5krhhOuO60vr%7Cl%2BGOEGAadQF3DncYbBtuGHcDcAlwH2scaxh3C3gLZBBnEGMMdw1%2B3n4%3D%7ClOCT%2FJkHcAl8CnYZhuOX4Jrtlu6Z6pzgl%2B%2BbJFcmXilRKVotVCdeJFErUCVdJLvescVlAKA%3D%7CleKNE3YTfAh8BHcYbxZsA3cLcAurCw%3D%3D%7CkuqFG34bdOqc5Jf4gPuBIU49UjdSPUs3QzeXNw%3D%3D%7Ck%2BSLFXDUctRm3m%2FYUOtY91P%2FTitEPUU9UideJEsxRjpJ6Uk%3D%7CkOiHGXzQY9SkEroeqw2m1njQbAlm%2BIH5juGU7JU1WilGI0YpUypZIoIi%7CkeuEGn%2FTYNenEbkdqA6l1XvTbwplEX4EfQh93X0%3D%7CjveYBmPPfMu7DaUBtBK5yWfPcxZ5BX8QYwx3C3AMrAw%3D%7Cj%2FaZB2LOfcq6DKQAtRO4yGbOchd4DH8LZBd4BHAEctJy%7CjPWaBGHNfsm5D6cDthC7y2XNcRR7B30SYQ5yBH4FpQU%3D%7CjfSbBWDMf8i4DqYCtxG6ymTMcBV6Dn0IZxR7B3sCdNR0%7CivOcAmfLeM%2B%2FCaEFsBa9zWPLdxJ9CXoPYBN8CHsPegCgAA%3D%3D%7Ci%2FKdA2bKec6%2BCKAEsRe8zGLKdhN8AHkWZQp%2BDXoPc9Nz%7CiPGeAGXJes29C6MHshS%2Fz2HJdRB%2FC3gPYBN8CHsCeQ6uDg%3D%3D%7CifCfAWTIe8y8CqIGsxW%2BzmDIdBF%2BCnkPYBN8CHwLeA%2BvDw%3D%3D%7Chv%2BQDmvHdMOzBa0JvBqxwW%2FHex5xDXEebQJ2AngOctJy%7Ch%2F6RD2rGdcKyBKwIvRuwwG7Geh9wBHcBbh1yBnMAdwurCw%3D%3D%7ChP2SDGnFdsGxB68Lvhizw23FeRxzD3QbaAdzBnAEft5%2B%7ChfKdA2YDbBBoHHMGdQ5hFWMWbBW1FQ%3D%3D%7CgvqVC24LZPqG%2FYHum%2BOXN1grRCFEK18pUytR8VE%3D%7Cg%2FmWCG3BcsW1A6sPuhy3x2nBfRh3BGsfaRJnErIS%7CgPeE95jrhPGe6p3phvCI8p3lmfaM9Jvvm%2ByD95jkkP%2BG6Z3qhfKd6Zzzhf2S55P8ifqV4Z3yhv2S5pzzh%2F6R5Z3yhvGe6pzzh%2FKd6Z3ygvaF6prvgPWBIQ%3D%3D&rdsToken=ZQZxPBNjD11T5GCrQTOq7vEmGChNojmG&account=15166087372&userId=&_input_charset=utf-8&r=1488939588620&ctoken=UPj4PQZD1MuwwBRq&_callback=arale.cache.callbacks.jsonp2";
        "https://shenghuo.alipay.com/home/queryUserStatus.json?timeSnap=1488939599833&ua=164n%2BqZ9mgNqgJnCG0Yus65w7XCvsi%2FyLDVddU%3D%7CnOiH84T%2FhPiO8orxjf6N8VE%3D%7CneiHGXz1WexE7V%2Fkge6L84fijehy3m%2FIesB2E7MT%7Cmu6b9JE4jfZu2aALkRijCaEXph2wLKHWqtxo9UTLR8tFz2fXpMFh%7Cm%2B%2BT%2FGIWeQ10AXoVYA9qwrjdfd0%3D%7CmOOM6Yws%7CmeWK74oq%7CluKW%2BWcCqR6pGqzRo9FnzGTVf9Rs3q8LpAi7H6PSdMZ2BLIaqwx%2BzWnVZc18y7kQvBO8zWbRYeNB8lXCfs1l5UHmQ%2BhM4UfjReNJ4UznTepO5krhhOuO60vr%7Cl%2BGOEGAadQF3DncYbBtuGHcDcAlwH2scaxh3C3gLZBBnEGMMdw1%2B3n4%3D%7ClOCT%2FJkHcAl8CnYZhuOX4Jrtlu6Z6pzgl%2B%2BbJFcmXilRKVotVCdeJFErUCVdJLvescVlAKA%3D%7Cle%2BAHnsecQJtEWgdvR0%3D%7CkuqFG37SYdamELgcqQ%2Bk1HrSbgtk%2BoP5g%2ByZ4Zk5ViVKL0olUypWI4Mj%7Ck%2BmGGH0YdwNsGmAWY8Nj%7CkOqFG37SYdamELgcqQ%2Bk1HrSbgtkEH8Jcwt%2F338%3D%7CkeiHGXzQY9SkEroeqw2m1njQbAlmGmAPfBNrEmoTsxM%3D%7CjveYBmPPfMu7DaUBtBK5yWfPcxZ5DX4JZhV6A3sCft5%2B%7Cj%2FaZB2LOfcq6DKQAtRO4yGbOchd4DH8IZxR7AngPdNR0%7CjPWaBGHNfsm5D6cDthC7y2XNcRR7D3wJZhV6A38GctJy%7CjfSbBWDMf8i4DqYCtxG6ymTMcBV6BnoVZglzD3MLqws%3D%7CivOcAmfLeM%2B%2FCaEFsBa9zWPLdxJ9AX0SYQ51AXgLqws%3D%7Ci%2FKdA2bKec6%2BCKAEsRe8zGLKdhN8AHwTYA90AnoMrAw%3D%7CiPGeAGXJes29C6MHshS%2Fz2HJdRB%2FC3gLZBd4A3oMedl5%7CifCfAWTIe8y8CqIGsxW%2BzmDIdBF%2BCnkKZRZ5AnkKf99%2F%7Chv%2BQDmvHdMOzBa0JvBqxwW%2FHex5xBXYFahl2DXELft5%2B%7Ch%2F6RD2rGdcKyBKwIvRuwwG7Geh9wDHUaaQZ6D3QMrAw%3D%7ChPOcAmcCbRFtGnUAeQxjF2QSZB6%2BHg%3D%3D%7Chf2SDGkMY%2F2J%2Bon%2FkOWd4UEuXTJXMl0pWiJUIIAg%7CgviXCWzAc8S0AqoOux22xmjAfBl2BWoebRRnHr4e%7Cg%2FSH9Jvoh%2FKd6Z7qhfOL8Z7mmvWP95jsmO%2BA9Jvnk%2FyK9pntmvWD%2BZbgmfaD9pnsmPeC8Z7qlvmN9pntl%2FiM9ZrulvmN%2BpXhl%2FiM%2BZbmmvWF8Z7rnj4%3D&rdsToken=fr8Ut6hWFxgesnxjYJ493qZnLIIKGem0&account=18863334440&userId=&_input_charset=utf-8&r=1488939599834&ctoken=UPj4PQZD1MuwwBRq&_callback=arale.cache.callbacks.jsonp3";
//        $url .= "&account=".$account;
        echo $url;
        $ret = curl_get($url);
        var_dump($ret);
    }
    /**
     * 方法一：通过循环遍历得到结果
     *  如果 N,M 比较大的话，此方法不建议使用，因为实在太LOW了
     */

    function getSucessUserNum()
    {
        $data = range(0, N);
        unset($data[0]);
        if(empty($data)) return false;

        //第一个开始报数的位置
        $p = P;

        while(count($data) > 1)
        {
            for($i=1; $i < M; $i++)
            {
                $p = (isset($data[$p])) ? $p : $this->getExistNumPosition($data, $p);
                $p++;
                $p = ($p == N) ? $p : $p % N;
            }

            $p = (isset($data[$p])) ? $p : $this->getExistNumPosition($data, $p);

            unset($data[$p]);

            $p = ($p == N) ? 1 : $p + 1;
        }

        $data = array_values($data);

        echo  "<br> successful num : " . $data[0] . "<br><br>";
    }
    /**
     * 获取下一个报数存在的下标
     * $data   当前存在的数据
     * $p  上一个报名数的下标
     */
    public function getExistNumPosition($data, $p)
    {
        if(isset($data[$p])) return $p;

        $p++;

        $p = ($p == N) ? $p : $p % N;

        return $this->getExistNumPosition($data, $p);
    }

    /**
     * 方法二：通过算法得到结果
     *  此方法比方法一快多了，不行自己试一下
     */
    public function getSucessUserNum_2()
    {
        $data = range(1, N);

        if(empty($data)) return false;

        //第一个报数的位置
        $start_p = (P-1);
        echo $start_p;
        while(count($data) > 1)
        {
            //报到数出列的位置
            $del_p = ($start_p  + M - 1) % count($data);

            if(isset($data[$del_p]))
            {
                unset($data[$del_p]);
            }
            else
            {
                break;
            }

            //数组从新排序
            $data = array_values($data);

            $new_count = count($data);

            //计算出在新的$data中，开始报数的位置
            $start_p = ($del_p >= $new_count) ? ($del_p % $new_count) : $del_p;
        }

        echo  "<br> successful num : " . $data[0] . "<br><br>";
    }










    public function test(){
        $all_count =  1000;
        $start_1 = rand(1,1000);
        $M = rand(1,500);
        echo $M;
        $data = range(0,$all_count);
        unset($data[0]);
        while(count($data) > 1){
            $del = ($start_1 + $M -1) % count($data);
            if($del <= count($data)){
                unset($data[$del]);
            }else{
                break;
            }
            $data = array_values($data);
            $start_1 = $del%count($data);

        }
        var_dump( $data[0] );
    }

















}