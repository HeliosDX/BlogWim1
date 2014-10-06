<?php
namespace Helios\BlogBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Helios\BlogBundle\Entity\Tag;

use Symfony\Component\HttpFoundation\Request;

class TagsTransformer implements DataTransformerInterface
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms a string (ftags) to an array of entities ($tags_arraytag).
     *
     * @param  string $ftags
     * @return array
     */
    public function reverseTransform($ftags)
    {
        if (!$ftags) {
            $ftags = ''; // default
        }

        $tags_arraytag = new ArrayCollection();
        $tags_arraystring = explode(",", $ftags);
        foreach ($tags_arraystring as $i => $tag_string) {

            // On vérifie si le tag est déjà dans la DB
            $issue = $this->om
                ->getRepository('HeliosBlogBundle:Tag')
                ->findOneByTag($tag_string)
            ;
            if (null === $issue) {
                $itag = new Tag();
                $itag->setTag($tag_string);
                if(!$tags_arraytag->contains($itag)) {
                    $tags_arraytag[$i] = $itag;
                }
            }
            else {
                if(!$tags_arraytag->contains($issue->getTag())) {
                    $tags_arraytag[$i] = $issue;
                }
            }
        }
        return $tags_arraytag;
    }

    /**
     * Transforms an array of entities ($tags_arraytag) to a string (ftags).
     *
     * @param  array $tags_arraytag
     * @return string
     */
    public function transform($tags_arraytag)
    {
        $ftags = "";
        if($tags_arraytag != null) {
            foreach($tags_arraytag as $tag) {
                $ftags = $ftags.', '.$tag->getTag();
                ?><script>alert("<?php echo $tag->getTag() ?>");</script><?php
            }

        }
        return $ftags;
    }
}