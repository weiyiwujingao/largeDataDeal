<?php
/**
@author cabing_2005@126.com
**/
class Simhash
{
    public $m_hash = null;
    public $hashbits = null;

    //构造函数
    public function __construct($tokens = array(), $hashbits = 128)
    {
        $this->m_hashbits = $hashbits;
        $this->m_hash = $this->simhash($tokens);
    }

    //to string
    public function __toString()
    {
        return strval($this->m_hash);
    }

    //返回hash值
    public function simhash($tokens)
    {
        if(!is_array($tokens))
        {
            throw new Exception("tokens should be array");
        }

        $v = array_fill(0,$this->m_hashbits,0);
        foreach($tokens as $x)
        {
            $x = $this->stringHash($x);
            for($i=0;$i<$this->m_hashbits;$i++)
            {
                $bitmask = gmp_init(1);
                gmp_setbit($bitmask, $i);
                $bitmask = gmp_sub($bitmask,1);
                if (gmp_strval(gmp_and($x,$bitmask)) != "0")
                {
                    $v[$i] += 1;
                }
                else
                {
                    $v[$i] -= 1;
                }
            }
        }
        $sum = 0;
        for($i=0;$i<$this->m_hashbits;$i++)
        {
            if ($v[$i] >= 0)
            {
                $num = gmp_init(1);
                gmp_setbit($num, $i);
                $num = gmp_sub($num,1);
                $sum = gmp_add($sum,$num);
            }
        }echo gmp_strval($sum).'<br>';
        return gmp_strval($sum);
    }

    //求海明距离
    public function hammingDistance($other)
    {
        $a = gmp_init($this->m_hash);
        $b = gmp_init($other->m_hash);

        $c = gmp_init(1);
        gmp_setbit($c, $this->m_hashbits);
        $c = gmp_sub($c,2);
        $x = gmp_and(gmp_xor($a,$b),$c);
        $tot = 0;
        while(gmp_strval($x))
        {
            $tot += 1;
            $x = gmp_and($x,gmp_sub($x,1));
        }
        return $tot;
    }

    //求相似度
    public function similarity ($other)
    {
        $a = floatval($this->m_hash);
        $b = floatval($other->m_hash);
        if($a > $b)
        {
            return $b/$a;
        }
        else
        {
            return $a/$b;
        }
    }
    
    public function stringHash($source)
    {
        if(empty($source))
        {
            return 0;
        }
        else
        {
            $x = ord($source[0]) << 7;
            
            $m = 1000003;
            
            $mask = gmp_sub(gmp_pow("2", $this->m_hashbits),1);
            $len = strlen($source);
            
            for($i=0;$i<$len;$i++)
            {
                $x = gmp_and(gmp_xor(gmp_mul($x,$m),ord($source[$i])),$mask);
            }
            $x = gmp_xor($x,$len);
            if(intval(gmp_strval($x)) == -1)
            {
                $x = -2;
            }
            return $x;
        }
    }
}


$s = '美国';

$hash1 = new Simhash(explode(" ",$s));

$s = '美国';

$hash2 = new Simhash(explode(" ",$s));


echo 'hash1:'.$hash1.',hash2:'.$hash2.'<br>';
echo($hash1->hammingDistance($hash2) . '|' . $hash1->similarity($hash2));