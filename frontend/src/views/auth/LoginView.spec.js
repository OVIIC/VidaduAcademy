import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import LoginView from './LoginView.vue'
import { ref, reactive } from 'vue'

// 1. Mock Router
vi.mock('vue-router', () => ({
  useRouter: () => ({ push: vi.fn() }),
  RouterLink: { template: '<a><slot /></a>' }
}))

// 2. Mock Store
vi.mock('@/stores/auth', () => ({
  useAuthStore: vi.fn(() => ({
    loading: false,
    login: vi.fn()
  }))
}))

// 3. Mock Composable
const { mockUseAuthLogic } = vi.hoisted(() => ({
  mockUseAuthLogic: vi.fn()
}))

vi.mock('@/composables/useAuthLogic', () => ({
  useAuthLogic: mockUseAuthLogic
}))

describe('LoginView', () => {
  // Default mock implementation
  beforeEach(() => {
    mockUseAuthLogic.mockReturnValue({
      form: reactive({ email: '', password: '', remember: false }),
      loading: ref(false),
      errors: ref({}),
      securityNotice: ref(''),
      rateLimitNotice: ref(''),
      clearSecurityNotice: vi.fn(),
      handleLogin: vi.fn()
    })
  })

  it('renders login form', () => {
    const wrapper = mount(LoginView, {
      global: {
        stubs: {
          RouterLink: true
        }
      }
    })
    
    expect(wrapper.find('input[type="email"]').exists()).toBe(true)
    expect(wrapper.find('input[type="password"]').exists()).toBe(true)
    expect(wrapper.text()).toContain('Prihlásiť sa')
  })

  it('updates form values on input', async () => {
    // We need to capture the reactive form object to verify updates
    const formState = reactive({ email: '', password: '', remember: false })
    mockUseAuthLogic.mockReturnValue({
      form: formState, // pass our local reactive object
      loading: ref(false),
      errors: ref({}),
      securityNotice: ref(''),
      rateLimitNotice: ref(''),
      clearSecurityNotice: vi.fn(),
      handleLogin: vi.fn()
    })

    const wrapper = mount(LoginView, { global: { stubs: { RouterLink: true } } })
    
    const emailInput = wrapper.find('input[type="email"]')
    await emailInput.setValue('test@example.com')

    expect(formState.email).toBe('test@example.com')
  })

  it('shows error messages', async () => {
    const errors = ref({})
    mockUseAuthLogic.mockReturnValue({
        form: reactive({ email: '', password: '' }),
        loading: ref(false),
        errors: errors,
        securityNotice: ref(''),
        rateLimitNotice: ref(''),
        clearSecurityNotice: vi.fn(),
        handleLogin: vi.fn()
    })

    const wrapper = mount(LoginView, { global: { stubs: { RouterLink: true } } })
    
    expect(wrapper.text()).not.toContain('Invalid email address')
    
    errors.value = { email: ['Invalid email address'] }
    await wrapper.vm.$nextTick()
    
    expect(wrapper.text()).toContain('Invalid email address')
  })
})
