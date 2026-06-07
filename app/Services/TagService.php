<?php

namespace App\Services;

use App\Models\Product;
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

    public function all(Product $product): Collection
    {
        return $product->tags;
    }

    /**
     * Resync the product tags
     *
     * Detach product tags if there's no payload or empty { tags: [] }
     */
    public function update(array $data, Product $product): Collection
    {
        $tags = $data['tags'] ?? [];

        // skip syncing tags if no input tags or the product has no tags
        if (! empty($tags) || $product->tags->isNotEmpty()) {
            $this->syncTags($product, $tags);

            // refresh to rehydrate the data
            $product->refresh();
        }

        return $product->tags;
    }

    public function syncTags(Product $product, array $tags): void
    {
        $newTags = $this->getNewTags($tags);

        // sync tags with have new tags
        if (count($newTags)) {
            $product->tags()->sync($newTags);
        }

        // if there's no tags, detach all product tags
        if (count($tags) === 0) {
            $product->tags()->detach();
        }

        // else, do nothing
    }

    /**
     * Save new tags in the DB and return the IDs of the given tags
     */
    private function getNewTags(array $tags = []): array
    {
        $newTags = [];

        // return empty array immediately if there's no new tag
        if (count($tags) === 0) {
            return $newTags;
        }

        $nonExistingTags = $this->getNonExistingTags($tags);

        $newTags =
            $nonExistingTags->count() > 0
                ? $this->saveNonExistingTags($nonExistingTags->all(), $tags)
                : $this->getExistingTags($tags)->all();

        return $newTags;
    }

    /**
     * Get all the existing tags in the DB based from the given array of tags
     *
     * @param  array  $field  = choose the returned collection values; either tag 'id' or 'name'
     */
    private function getExistingTags(array $tags, string $field = 'id'): Collection
    {
        $this->assertShouldBeInArray(['id', 'name'], $field);

        return Tag::whereIn('name', $tags)->get()->map(fn (Tag $tag) => $tag[$field]);
    }

    /**
     * Get the tags that are not saved in the DB based from the given array of tags
     */
    private function getNonExistingTags(array $tags): Collection
    {
        $tagsCollection = collect($tags)->unique();

        // return empty collection immediately if there's no new tag
        if ($tagsCollection->count() === 0) {
            return $tagsCollection;
        }

        $existingTags = $this->getExistingTags($tags, 'name');

        return $tagsCollection->diff($existingTags)->transform(fn (string $newTag) => ['name' => $newTag])->values();
    }

    /**
     * Save non-existing tags and return their tag IDs
     */
    private function saveNonExistingTags(array $nonExistingTags, array $tags): array
    {
        $user = Auth::guard('web')->user();

        // if have new tag, insert to DB
        $user->tags()->createMany([$nonExistingTags]);

        // get all tag ids.
        return Tag::whereIn('name', $tags)->get()->map(fn (Tag $tag) => $tag->id)->all();
    }
}
