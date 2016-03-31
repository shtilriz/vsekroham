<?php namespace Gufy\PdfToHtml;

use PHPHtmlParser\Dom;
use Pelago\Emogrifier;
class Html extends Dom
{
  protected $contents, $total_pages, $current_page, $pdf_file, $locked = false;
  public function __construct($pdf_file)
  {
    $this->getContents($pdf_file);
    return $this;
  }
  private function getContents($pdf_file)
  {
    $this->locked = true;
    // echo $file;
    $info = new Pdf($pdf_file);
    $pdf = new Base($pdf_file, array(
      'singlePage'=>true,
      'noFrames'=>false,
    ));
    $pages = $info->getPages();
    // print_r($pages);
    // print_r($pages);
    $random_dir = uniqid();
    $outputDir = Config::get('pdftohtml.output', dirname(__FILE__).'/../output/'.$random_dir);
    if(!file_exists($outputDir))
    mkdir($outputDir, 0777, true);
    $pdf->setOutputDirectory($outputDir);
    $pdf->generate();
    $fileinfo = pathinfo($pdf_file);
    $base_path = $pdf->outputDir.'/'.$fileinfo['filename'];
    $contents = array();
    for($i=1;$i<=$pages;$i++)
    {
      $content = file_get_contents($base_path.'-'.$i.'.html');
      if($this->inlineCss())
      {
        // $content = str_replace(array('<!--','-->'),'',$content);
        $parser = new Emogrifier($content);
        // print_r($parser);
        $content = $parser->emogrify();
      }
      file_put_contents($base_path.'-'.$i.'.html', $content);
      $contents[$i] = file_get_contents($base_path.'-'.$i.'.html');
    }
    $this->contents = $contents;
    $this->goToPage(1);
  }
  public function goToPage($page=1)
  {
    if($page>count($this->contents))
    throw new \Exception("You're asking to go to page {$page} but max page of this document is ".count($this->contents));
    $this->current_page = $page;
    return $this->load($this->contents[$page]);
  }
  public function getTotalPages()
  {
    return count($this->contents);
  }
  public function getCurrentPage()
  {
    return $this->current_page;
  }
  public function inlineCss()
  {
    return Config::get('pdftohtml.inlineCss', true);
  }
}
