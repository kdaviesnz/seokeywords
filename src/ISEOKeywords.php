<?php


interface ISEOKeywords
{

    public function getCount4PhraseWords(string $content);

    public function getCount3PhraseWords(string $content);

    public function getCount2PhraseWords(string $content);

    public function get_google_lsi(string $primary_keyword, array $words, int $depth);
    public function get_lsi_keywords( string $primary_keyword);

    public function get_stop_words();


}