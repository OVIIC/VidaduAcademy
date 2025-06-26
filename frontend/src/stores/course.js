import { defineStore } from 'pinia'
import { courseService } from '@/services'
import { useToast } from 'vue-toastification'

const toast = useToast()

export const useCourseStore = defineStore('course', {
  state: () => ({
    courses: [],
    featuredCourses: [],
    categories: [],
    currentCourse: null,
    loading: false,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 12,
      total: 0,
    },
    filters: {
      search: '',
      category: '',
      difficulty: '',
      min_price: null,
      max_price: null,
      sort_by: 'created_at',
      sort_order: 'desc',
    },
  }),

  getters: {
    hasMorePages: (state) => state.pagination.current_page < state.pagination.last_page,
    
    filteredCourses: (state) => {
      let filtered = [...state.courses]
      
      if (state.filters.search) {
        const search = state.filters.search.toLowerCase()
        filtered = filtered.filter(course => 
          course.title.toLowerCase().includes(search) ||
          course.description.toLowerCase().includes(search)
        )
      }
      
      return filtered
    },
  },

  actions: {
    async fetchCourses(params = {}) {
      this.loading = true
      try {
        const response = await courseService.getAllCourses({
          ...this.filters,
          ...params,
        })
        
        if (params.page && params.page > 1) {
          // Append for pagination
          this.courses.push(...response.data)
        } else {
          // Replace for new search/filter
          this.courses = response.data
        }
        
        this.pagination = {
          current_page: response.current_page,
          last_page: response.last_page,
          per_page: response.per_page,
          total: response.total,
        }
      } catch (error) {
        console.error('Failed to fetch courses:', error)
        toast.error('Failed to load courses')
      } finally {
        this.loading = false
      }
    },

    async fetchFeaturedCourses() {
      try {
        this.featuredCourses = await courseService.getFeaturedCourses()
      } catch (error) {
        console.error('Failed to fetch featured courses:', error)
      }
    },

    async fetchCategories() {
      try {
        this.categories = await courseService.getCategories()
      } catch (error) {
        console.error('Failed to fetch categories:', error)
      }
    },

    async fetchCourse(slug) {
      this.loading = true
      try {
        this.currentCourse = await courseService.getCourse(slug)
        return this.currentCourse
      } catch (error) {
        console.error('Failed to fetch course:', error)
        toast.error('Course not found')
        throw error
      } finally {
        this.loading = false
      }
    },

    updateFilters(newFilters) {
      this.filters = { ...this.filters, ...newFilters }
      this.fetchCourses({ page: 1 })
    },

    clearFilters() {
      this.filters = {
        search: '',
        category: '',
        difficulty: '',
        min_price: null,
        max_price: null,
        sort_by: 'created_at',
        sort_order: 'desc',
      }
      this.fetchCourses({ page: 1 })
    },

    loadMoreCourses() {
      if (this.hasMorePages && !this.loading) {
        this.fetchCourses({ page: this.pagination.current_page + 1 })
      }
    },
  },
})
