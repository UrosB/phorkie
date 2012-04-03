<?php
namespace Phorkie;

class File
{
    /**
     * Full path to the file
     *
     * @var string
     */
    public $path;

    /**
     * Repository this file belongs to
     *
     * @var string
     */
    public $repo;

    public function __construct($path, Repository $repo = null)
    {
        $this->path = $path;
        $this->repo = $repo;
    }

    /**
     * Get filename relative to the repository path
     *
     * @return string
     */
    public function getFilename()
    {
        return basename($this->path);
    }

    /**
     * Return the full path to the file
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get file extension without dot
     *
     * @return string
     */
    public function getExt()
    {
        return substr($this->path, strrpos($this->path, '.') + 1);
    }

    public function getContent()
    {
        return file_get_contents($this->path);
    }

    public function getHighlightedContent()
    {
        /**
         * Yes, geshi needs to be in your include path
         * We use the mediawiki geshi extension package.
         */
        require_once 'MediaWiki/geshi/geshi/geshi.php';
        $geshi = new \GeSHi($this->getContent(), $this->getGeshiType());
        $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
        $geshi->set_header_type(GESHI_HEADER_DIV);
        return $geshi->parse_code();
    }

    /**
     * Get a link to the file
     *
     * @param string $type Link type. Supported are:
     *                     - "raw"
     *                     - "display"
     *
     * @return string
     */
    public function getLink($type)
    {
        if ($type == 'raw') {
            return '/' . $this->repo->id . '/raw/' . $this->getFilename();
        }
        throw new Exception('Unknown type');
    }

    /**
     * Returns the type of the file, as used by Geshi
     *
     * @return string
     */
    public function getGeshiType()
    {
        $ext = $this->getExt();
        if (isset($GLOBALS['phorkie']['languages'][$ext]['geshi'])) {
            $ext = $GLOBALS['phorkie']['languages'][$ext]['geshi'];
        }

        return $ext;
    }

    public function getMimeType()
    {
        $ext = $this->getExt();
        if (!isset($GLOBALS['phorkie']['languages'][$ext])) {
            return null;
        }
        return $GLOBALS['phorkie']['languages'][$ext]['mime'];
    }
}

?>