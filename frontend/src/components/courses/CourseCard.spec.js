import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import CourseCard from './CourseCard.vue'

describe('CourseCard', () => {
  it('renders title and instructor correctly', () => {
    const wrapper = mount(CourseCard, {
      props: {
        course: {
          id: 1,
          title: 'Vue 3 Masterclass',
          short_description: 'Learn Vue 3',
          duration_minutes: 120,
          instructor: { name: 'Alex' },
          thumbnail: '/test.jpg'
        }
      },
      global: {
        stubs: {
          LazyImage: true,
          StarIcon: true,
          ClockIcon: true
        }
      }
    })
    
    expect(wrapper.text()).toContain('Vue 3 Masterclass')
    expect(wrapper.text()).toContain('Alex')
    expect(wrapper.text()).toContain('2h') // 120 mins = 2h
  })
})
