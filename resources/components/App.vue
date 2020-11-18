<template>
  <div class="app">
    <header class="header">
      Paytabs Test
    </header>
    <nav class="nav">
      <label>
        Navigation Path:
      </label>
      <div
        v-for="(path, index) in paths"
        :key="'path' + index"
        class="navItem"
      >
        <span
          v-if="index !== 0 && path && path.id"
          :key="'nav' + (path ? path.id : 0)"
          class="navItem__arrow"
        >
          ⇨
        </span>
        <code
          :key="'code' + (path ? path.id : 0)"
          class="navItem__label"
          @click="selectCategory(path)"
        >
          {{ path ? path.name : 'Main Category' }}

          <span class="navItem__count">
            {{ countDirectChilds(path ? path.id : 0) }}
          </span>
        </code>
      </div>
    </nav>
    <main class="main">
      <div
        v-for="(category,index) in navigatableCategories"
        :key="'category' + index"
        class="category"
      >
        <h4
          class="categoryTitle"
          @click="selectCategory(category)"
        >
          {{ category.name || 'Main Category' }}
          <span
            v-if="countDirectChilds(category.id) > 0"
            class="categoryCount"
          >
            ({{ countDirectChilds(category.id) }})
          </span>
        </h4>
        <ul class="subCategories">
          <li
            v-for="subCategory in category.subCategories"
            :key="subCategory.id"
            :class="`subCategory__item ${paths.map(x => x ? x.id : '0').includes(subCategory.id) ? 'subCategory__item--selected' : ''}`" 
            @click="selectCategory(subCategory)"
          >
            <span class="subCategory__item__name">
              {{ subCategory.name }}
            </span>
            <code
              v-if="countDirectChilds(subCategory.id) > 0"
              class="badge"
            >
              {{ countDirectChilds(subCategory.id) }}
            </code>
          </li>
          <li class="subCategory__addItem">
            <form
              class="subCategory__addItem__form"
              @submit.prevent="add(category.id)"
            >
              <input
                class="subCategory__addItem__input"
                type="text"
                value=""
                placeholder="Category Name"
                :data-category-id="category.id"
              >
              <button
                class="subCategory__addItem__button"
                type="submit"
              >
                Add
              </button>
            </form>
          </li>
        </ul>
      </div>
    </main>
    <footer class="footer">
      Code: <a href="https://github.com/Mohannad-tests/paytabs">GitHub</a>
    </footer>
  </div>
</template>
<script>
import groupBy from 'lodash/groupBy'

export default {
    components: {

    },
    props: {
    },
    data() {
        return {
          categories: [],
          paths: [],
          groupBy,
        }
    },
    computed: {
        navigatableCategories() {
            const includedCategories = this.categories
            const categoriesGroupedByParent = this.groupBy(includedCategories, 'parent_id')

            let paths = [... new Set(this.paths.map(x => (x && (x.id)) || '0'))]

            return paths.map(parentId => {
                let parent = this.categories.find(x => x.id === parentId)

                return {
                    id: parent ? parent.id : '0',
                    parent_id: parentId,
                    name: parent ? parent.name : 'Main Category',
                    subCategories: categoriesGroupedByParent[parentId],
                }
            })

            return Object.keys(categoriesGroupedByParent).map(parentId => {
                return {
                    parent_id: parentId,
                    name: this.categories.find(x => x.id === parentId),
                    subCategories: categoriesGroupedByParent[parentId],
                }
            })
        },
    },
    mounted() {
        this.fetchCategories()
    },
    methods: {
        // UI Functions
        selectCategory(category) {
            if (category && category.id !== '0')
            {
                this.select(category)
            }

            this.paths = this.findParents(category)
            if (category && category.id !== '0' && (category && category.id && ! this.paths.map(x => x ? x.id : '0').includes(category.id)))
            {
                this.paths.push(category)
            }
        },

        // Logic Functions
        findParents(category) {
            const parents = []

            const setParentsRecursively = (category) => {
                const parent = category && this.categories.find(y => y.id === category.parent_id)

                parents.push( parent)

                if ( parent) {
                    return setParentsRecursively(parent)
                }
            }

            setParentsRecursively(category)

            return parents.reverse()
        },
        countDirectChilds(categoryId) {
            return this.categories.filter(x => x.parent_id === categoryId).length
        },

        // Request/Response functions
        fetchCategories() {
            axios.get('/all')
                .then(resp => {
                    this.categories = resp.data
                    if (this.paths.length === 0)
                    {
                        this.selectCategory(null)
                    }
                })
        },
        add(categoryId) {
            let name = $(this.$el).find(`[data-category-id=${categoryId}]`).val()

            $(this.$el).find(`[data-category-id=${categoryId}]`).val('')

            axios.post('/add', {
                name,
                parent_id: categoryId,
                csrf: this.csrfToken,
            })
            .then(resp => this.fetchCategories())
        },
        select(category) {
            axios.get('/select?id=' + category.id)
                .then(resp => this.fetchCategories())
        },
    }
}
</script>