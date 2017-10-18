<?php

require_once("vendor/autoload.php");
require_once("src/ISEOKeywords.php");
require_once("src/SEOKeywords.php");

class SEOKeywordsTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testSEOKeywords()
    {
        $content = "President Donald Trump disavowed a bipartisan health care agreement on Wednesday, delivering it a likely fatal blow only a day after he praised its approach and said he had personally encouraged its negotiation.

Sens. Lamar Alexander, R-Tenn., and Patty Murray, D-Wash., had spent weeks working on the bill, which was designed to stabilize insurance markets under Obamacare by guaranteeing cost-sharing reduction (CSR) payments that Trump had recently cut off. In exchange for Republican cooperation, Democrats would agree to modest measures loosening Obamacare regulations and allowing insurers to sell less comprehensive plans to more customers. Schumer linked it to a broader pattern of inconsistency, saying Trump had also projected a willingness to make a deal with Democrats to protect young undocumented immigrants, or Dreamers, only to retreat to more hardline demands later on.The CSR payments reimburse insurers for reducing deductibles for low-income customers, but the companies must provide the same benefits under the law regardless of whether they continue. Insurers in most states have already raised premiums significantly for 2018 based on the explicit assumption that Trump, who had long threatened to cut off CSR spending, would eventually do so.";


        $keywordTool = new \kdaviesnz\seokeywords\SEOKeywords();

        $count4Phrasewords = $keywordTool->getCount4PhraseWords($content);
        $count3Phrasewords = $keywordTool->getCount3PhraseWords($content);
        $count2Phrasewords = $keywordTool->getCount2PhraseWords($content);

        var_dump($count4Phrasewords);
        var_dump($count3Phrasewords);
        var_dump($count2Phrasewords);

        $lsi= $keywordTool->get_lsi_keywords("Trump");
        var_dump($lsi);

        $stopwords = $keywordTool->get_stop_words();




    }

}
