import { defineStore } from 'pinia'
import { courseService } from '@/services'
import { useToast } from 'vue-toastification'
import { apiCache } from '@/utils/cache'

const toast = useToast()

export const useCourseStore = defineStore('course', {
  state: () => ({
    courses: [],
    featuredCourses: [],
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
      console.log('fetchCourses called with params:', params)
      this.loading = true
      try {
        console.log('Making API call...')
        const response = await courseService.getAllCourses({
          ...this.filters,
          ...params,
        })
        console.log('API response:', response)
        
        if (params.page && params.page > 1) {
          // Append for pagination - response už je paginated objekt
          this.courses.push(...(response.data || []))
        } else {
          // Replace for new search/filter - response už je paginated objekt
          this.courses = response.data || []
        }
        console.log('Courses after setting:', this.courses)
        
        this.pagination = {
          current_page: response.current_page,
          last_page: response.last_page,
          per_page: response.per_page,
          total: response.total,
        }
      } catch (error) {
        console.error('Failed to fetch courses:', error)
        toast.error('Načítanie kurzov sa nepodarilo')
      } finally {
        this.loading = false
        console.log('Loading set to false')
      }
    },

    async fetchFeaturedCourses() {
      try {
        this.featuredCourses = await courseService.getFeaturedCourses()
      } catch (error) {
        console.error('Failed to fetch featured courses:', error)
      }
    },

    async fetchCourse(slug) {
      this.loading = true
      try {
        this.currentCourse = await courseService.getCourse(slug)
        return this.currentCourse
      } catch (error) {
        console.error('Failed to fetch course:', error)
        toast.error('Kurz sa nenašiel')
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

    async refreshCourses() {
      // Clear cache for courses
      apiCache.clear()
      
      // Fetch fresh data
      await this.fetchCourses({ page: 1 })
      await this.fetchFeaturedCourses()
      
      console.log('Courses refreshed from server')
    },
  },
})
