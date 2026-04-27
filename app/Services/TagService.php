<?php

namespace App\Services;

use App\Models\Tag;
use App\Traits\AssertionTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TagService
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Save new tags in the DB and return the IDs of the given tags
     *
     * @param array $tags
     * @return array
     */
    public function getNewTags(array $tags = []): array
    {
        $newTags = [];

        // return empty array immediately if there's no new tag
        if (count($tags) === 0) {
            return $newTags;
        }

        $nonExistingTags = $this->getNonExistingTags($tags);

        if ($nonExistingTags->count() > 1) {
            // if have new tag, insert to DB
            Tag::insert($nonExistingTags->all());

            // get all tag ids.
            $newTags = Tag::whereIn('name', $tags)
                ->get()
                ->map(fn(Tag $tag) => $tag->id)
                ->all();
        } else {
            $newTags = $this->getExistingTags($tags)->all();
        }

        return $newTags;
    }

    /**
     * Get all the existing tags in the DB based from the given array of tags
     *
     * @param array $tags
     * @param array $field = choose the returned collection values; either tag 'id' or 'name'
     * @return Collection
     */
    public function getExistingTags(
        array $tags,
        string $field = 'id',
    ): Collection {
        $this->assertShouldBeInArray(['id', 'name'], $field);

        return Tag::whereIn('name', $tags)
            ->get()
            ->filter(fn(Tag $tag) => in_array($tag->name, $tags))
            ->map(fn(Tag $tag) => $tag[$field]);
    }

    /**
     * Get the tags that are not saved in the DB based from the given array of tags
     *
     * @param array $tags
     * @return Collection
     */
    public function getNonExistingTags(array $tags): Collection
    {
        $tagsCollection = collect($tags);

        // return empty array immediately if there's no new tag
        if ($tagsCollection->count() === 0) {
            return $tagsCollection;
        }

        $existingTags = $this->getExistingTags($tags, 'name');

        return $tagsCollection
            ->diff($existingTags)
            ->transform(function (string $newTag) {
                return [
                    'name' => $newTag,
                    'added_by' => Auth::guard('web')->user()->id,
                    'created_at' => now(),
                ];
            })
            ->values();
    }
}
