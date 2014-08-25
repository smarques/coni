<?
// This is a template for a PHP scraper on Morph (https://morph.io)
// including some code snippets below that you should find helpful

require 'scraperwiki.php';
require 'scraperwiki/simple_html_dom.php';
//
// // Read in a page
// $html = scraperwiki::scrape("http://foo.com");
//
// // Find something on the page using css selectors
// $dom = new simple_html_dom();
// $dom->load($html);
// print_r($dom->find("table.list"));
//
// // Write out to the sqlite database using scraperwiki library
// scraperwiki::save_sqlite(array('name'), array('name' => 'susan', 'occupation' => 'software developer'));
//
// // An arbitrary query against the database
// scraperwiki::select("* from data where 'name'='peter'")

// You don't have to do things with the ScraperWiki library. You can use whatever is installed
// on Morph for PHP (See https://github.com/openaustralia/morph-docker-php) and all that matters
// is that your final data is written to an Sqlite database called data.sqlite in the current working directory which
// has at least a table called data.
$url = 'http://www.coni.it/it/?option=com_societasportiveconi&view=societasportiveconi&Itemid=566&tipoOrganismo=0&siglaOrganismo=0&regione=&siglaProvincia=&numeroIscrizione=&codice_affiliazione=&denominazione=&codice_fiscale=&ricercaSocieta=Avvia+ricerca';

function scrapeBaby($url)
{
    $html = scraperwiki::scrape($url);


    $dom = new simple_html_dom();
    $dom->load($html);
    $all = ($dom->find("table.societa"));
    foreach($all AS $count => $data)
    {
        $rigona = array();
        $tdsocieta = $data->find("td.nomeSoc");
        if(sizeof($tdsocieta))
        {
            $nomesocieta = $tdsocieta[0]->plaintext ;
            echo 'Parsing:'.$nomesocieta."<br>\n";
            $rigona['nome'] = $nomesocieta;
        }
        $dati = $tdsocieta = $data->find("tr.riga");
        foreach($dati AS $count2 => $datiriga)
        {
           $nomecampo = $datiriga->find('td.ncampo');
           $dato = $datiriga->find('td.dato');
            if(sizeof($nomecampo) && sizeof($dato))
            {
                $rigona[$nomecampo[0]->plaintext] = $dato[0]->plaintext;
            }
        }
        $rigona['iscrizione'] = $rigona['Numero iscrizione:'];
        unset($rigona['Numero iscrizione:'];
        scraperwiki::save_sqlite('iscrizione', $rigona);
        //print_r($rigona['Numero iscrizione:']);
    }
     $next = ($dom->find("p.paginazione a"));
    if(sizeof($next))
    {
        $paginateForward = $next[sizeof($next)-1];
        $nextUrl = $paginateForward->attr['href'];
        scrapeBaby($nextUrl);
    }
}
scrapeBaby($url);
    

