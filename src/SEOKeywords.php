<?php
declare(strict_types=1);

namespace kdaviesnz\seokeywords;


class SEOKeywords implements \ISEOKeywords
{

    /**
     * SEOKeywords constructor.
     * @param $content
     */
    public function __construct()
    {

    }

    public function get_stop_words() {

        $stopwords = array();

        $handle = fopen( 'src/stopwords.txt', 'r' );
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                $stopword = trim( $buffer );
                if ( ! empty( $stopword ) ) {
                    $stopwords[] = $stopword;
                }
            }
            if (!feof($handle)) {
                throw new \Exception( "Error: unexpected fgets() fail\n" );
            }
            fclose($handle);
        }

        return $stopwords;
    }

    /**
     * @param string $primary_keyword
     * @return array
     * @see https://docs.google.com/document/d/1gB_kmEIcCK08o02_QontQeGNYySqiKviXpNqyg9UGWs/edit?ts=58627e15#heading=h.za5xhxcyjvs0
     * @see https://www.quora.com/LSI-Keywords-Does-anyone-know-tool-to-search-good-LSI-keywords
     * @see http://lsigraph.com
     * @see http://www.nichelaboratory.com
     * @see http://semantic-link.com/related.php?word=cats
     */
    public  function get_lsi_keywords(string $primary_keyword)
    {

        $primary_keyword = strtolower($primary_keyword);

        $words = array();

        /*
         * LSI keywords (Latent Semantic Indexing) are basically keywords that are semantically related to your primary keyword. Contrary to popular belief, they are NOT just synonym or keywords that are similar in meaning. LSI keywords are also keywords that are sentimentally related with your primary keyword (there's a whole bunch of algorithm to determine this, but let's not get into it.)
         */
        $url = 'http://semantic-link.com/related.php?word=' . urlencode( $primary_keyword );
        $headers = array();
        $client = new \GuzzleHttp\Client();
        $request = new \GuzzleHttp\Psr7\Request('GET', $url, $headers);

        $result = $client->send($request);
        $items = json_decode($result->getBody()->getContents());
        foreach( $items as $item ) {
            $words[] = $item->v;
        }

        // Add words from google
        $words = $this->get_google_lsi( $primary_keyword, $words, 1 );

        return $words;

    }


    public function get_google_lsi(string $primary_keyword, array $words = array(), int $depth = 0):array
    {
        $url = 'https://www.google.co.nz/complete/search?sclient=psy-ab&biw=&bih=&q=' . urlencode($primary_keyword);
        $request = new \GuzzleHttp\Psr7\Request('GET', $url, array() );
        $client = new \GuzzleHttp\Client();
        $result = $client->send($request);
        $temp = json_decode( $result->getBody()->getContents() );
        if (count($temp[1] > 1 ) ) {
            unset( $temp[1][0] );
            foreach( $temp[1] as $item ) {
                $words[] = $item[0];
            }
        }
        return $words;
    }


    public function getCount2PhraseWords(string $content) {

        $count_2_phraseWords = array();

        $content = str_replace("   ", " ", $content);

        $content_arr = explode(' ',$content);

        for ($co_stp = 0; $co_stp < count($content_arr); $co_stp++) {

            //    if (substr_count(strtolower($this->user_stop_word), $content_without_stopwords_arr[$co_stp]) == 0) {
            $next_wrd = $co_stp + 1;

            if (isset($content_sarr[$co_stp]) && isset($content_sarr[$next_wrd])) {
                $phraseword = trim($$content_sarr[$co_stp]) . ' ' . trim($content_sarr[$next_wrd]);
            } elseif (isset($content_sarr[$co_stp]) && !isset($content_sarr[$next_wrd])) {
                $phraseword = trim($content_sarr[$co_stp]);
            }

            if (!empty($phraseword)) {
                $count_2_phraseWords[$phraseword] = substr_count($content, $phraseword);
            }
            //    }
        }
        //   }

        asort($count_2_phraseWords);
        arsort($count_2_phraseWords);
        return $count_2_phraseWords;
    }


    public function getCount3PhraseWords(string $content)
    {

        $count_3_phraseWords = array();

        $content = str_replace("   ", " ", $content);

        $content_arr = explode(' ', $content);

        for ($co_stp_3 = 0; $co_stp_3 < count($content_arr); $co_stp_3++) {
            $middle_wrd = $co_stp_3 + 1;
            $next_wrd = $co_stp_3 + 2;

            if (isset($this->content_with_stopwords_arr[$co_stp_3])) {
                $content_with_stopwords_arr_co_stp_3 = $content_arr[$co_stp_3];
            } else {
                $content_with_stopwords_arr_co_stp_3 = '';
            }

            if (isset($content_arr[$middle_wrd])) {
                $content_with_stopwords_arr_middle_wrd = $content_arr[$middle_wrd];
            } else {
                $content_with_stopwords_arr_middle_wrd = '';
            }

            if (isset($content_arr[$next_wrd])) {
                $content_with_stopwords_arr_next_wrd = $content_arr[$next_wrd];
            } else {
                $content_with_stopwords_arr_next_wrd = '';
            }

            $phraseword3 = trim($content_with_stopwords_arr_co_stp_3) . ' ' . trim($content_with_stopwords_arr_middle_wrd) . ' ' . trim($content_with_stopwords_arr_next_wrd);

            $count_3_phraseWords[$phraseword3] = substr_count($content, $phraseword3);
        }


        $count_3_phraseWords = array_filter( array_keys($count_3_phraseWords), function( $item ) {
            return str_word_count($item) == 3;
        });


        asort($count_3_phraseWords);
        arsort($count_3_phraseWords);


        return $count_3_phraseWords;
    }

    public function getCount4PhraseWords(string $content) {

        $count_4_phraseWords = array();
        
        $content = str_replace("   ", " ", $content);

        $content_arr = explode(' ', $content);
        
        for ($co_stp_4 = 0; $co_stp_4 < count($content_arr); $co_stp_4++) {

            $second_wrd = $co_stp_4 + 1;
            $third_wrd = $co_stp_4 + 2;
            $last_wrd = $co_stp_4 + 3;

            if (isset($content_arr[$co_stp_4])) {
                $content_with_stopwords_arr_co_stp_4 = $content_arr[$co_stp_4];
            } else {
                $content_with_stopwords_arr_co_stp_4 = '';
            }

            if (isset($content_arr[$second_wrd])) {
                $content_with_stopwords_arr_second_wrd = $content_arr[$second_wrd];
            } else {
                $content_with_stopwords_arr_second_wrd = '';
            }

            if (isset($content_arr[$third_wrd])) {
                $content_with_stopwords_arr_third_wrd = $content_arr[$third_wrd];
            } else {
                $content_with_stopwords_arr_third_wrd = '';
            }

            if (isset($content_arr[$last_wrd])) {
                $content_with_stopwords_arr_last_wrd = $content_arr[$last_wrd];
            } else {
                $content_with_stopwords_arr_last_wrd = '';
            }

            $phraseword4 = trim($content_with_stopwords_arr_co_stp_4) . ' ' . trim($content_with_stopwords_arr_second_wrd) . ' ' . trim($content_with_stopwords_arr_third_wrd) . ' ' . trim($content_with_stopwords_arr_last_wrd);

            $count_4_phraseWords[$phraseword4] = substr_count($content, $phraseword4);
        }
        asort($count_4_phraseWords);
        arsort($count_4_phraseWords);

        return $count_4_phraseWords;
    }


}