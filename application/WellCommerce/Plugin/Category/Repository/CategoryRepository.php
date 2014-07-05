<?php
/*
 * WellCommerce Open-Source E-Commerce Platform
 *
 * This file is part of the WellCommerce package.
 *
 * (c) Adam Piotrowski <adam@wellcommerce.org>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace WellCommerce\Plugin\Category\Repository;

use WellCommerce\Core\Component\Repository\AbstractRepository;
use WellCommerce\Plugin\Category\Model\Category;
use WellCommerce\Plugin\Category\Model\CategoryTranslation;

/**
 * Class CategoryAbstractRepository
 *
 * @package WellCommerce\Plugin\Category\AbstractRepository
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
class CategoryRepository extends AbstractRepository implements CategoryRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return Category::with('translation', 'shop')->get();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Category::findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlug($slug, $language = null)
    {
        $language = (null != $language) ? : $this->getCurrentLanguage();

        $category = Category::loadBySlug($slug, $language)->filterBy('shop', 'shop_id', 6);

        return $category->first();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        // delete function triggered from xajax
        if (is_array($id) && isset($id['id'])) {
            $id = (int)$id['id'];
        }

        $category = $this->find($id);
        $category->delete();
        $this->dispatchEvent(CategoryRepositoryInterface::POST_DELETE_EVENT, $category);
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data, $id = null)
    {
        $this->transaction(function () use ($data, $id) {
            $category = Category::firstOrCreate([
                'id' => $id
            ]);

            $data = $this->dispatchEvent(CategoryRepositoryInterface::PRE_SAVE_EVENT, $category, $data);

            $category->update($data);

            foreach ($this->getLanguageIds() as $language) {
                $translation = CategoryTranslation::firstOrCreate([
                    'category_id' => $category->id,
                    'language_id' => $language
                ]);

                $translationData = $translation->getTranslation($data, $language);
                $translation->update($translationData);
            }

            $category->sync($category->shop(), $data['shops']);

            $this->dispatchEvent(CategoryRepositoryInterface::POST_SAVE_EVENT, $category, $data);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function quickAddCategory($request)
    {
        $id = $this->transaction(function () use ($request) {
            $category            = new Category();
            $category->enabled   = 1;
            $category->hierarchy = 0;
            $category->parent_id = isset($request['parent']) ? $request['parent'] : 0;
            $category->save();

            $translation              = new CategoryTranslation();
            $translation->category_id = $category->id;
            $translation->language_id = $this->getCurrentLanguage();
            $translation->name        = $request['name'];
            $translation->slug        = $request['name'];
            $translation->save();

            return $category->id;
        });

        return [
            'id' => $id
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function changeCategoryOrder($request)
    {
        $this->transaction(function () use ($request) {
            foreach ($request['items'] as $item) {
                $category            = Category::findOrFail($item['id']);
                $category->parent_id = $item['parent'];
                $category->hierarchy = $item['weight'];
                $category->save();
            }
        });

        return [
            'status' => $this->trans('Category order saved successfully.')
        ];
    }

    /**
     * Returns categories tree
     *
     * @return array
     */
    public function getCategoriesTree()
    {
        if ($this->getCache()->hasItem('categoriesTree')) {
            return $this->getCache()->getItem('categoriesTree');
        }

        $categories     = $this->all();
        $categoriesTree = [];

        foreach ($categories as $category) {

            $children     = Category::children($category->id)->get();
            $languageData = $category->translation->getCurrentTranslation($this->getCurrentLanguage());

            $link = $this->generateUrl('front.category.index', ['slug' => $languageData->slug]);

            $categoriesTree[$category->id] = [
                'id'          => $category->id,
                'name'        => $languageData->name,
                'link'        => $link,
                'hasChildren' => count($children),
                'parent'      => $category->parent_id,
                'weight'      => $category->hierarchy
            ];
        }

        $this->getCache()->addItem('categoriesTree', $categoriesTree);

        return $categoriesTree;
    }
}