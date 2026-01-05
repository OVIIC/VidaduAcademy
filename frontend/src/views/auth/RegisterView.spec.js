import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import RegisterView from './RegisterView.vue'

// 1. Mock Router
vi.mock('vue-router', () => ({
  useRouter: () => ({ push: vi.fn() }),
  RouterLink: { template: '<a><slot /></a>' }
}))

// 2. Mock Toast
const mockToast = { error: vi.fn() }
vi.mock('vue-toastification', () => ({
  useToast: () => mockToast
}))

// 3. Mock Store
const mockRegister = vi.fn()
vi.mock('@/stores/auth', () => ({
  useAuthStore: vi.fn(() => ({
    loading: false,
    register: mockRegister
  }))
}))

describe('RegisterView', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('renders registration form', () => {
    const wrapper = mount(RegisterView, { global: { stubs: { RouterLink: true } } })
    
    expect(wrapper.find('input[type="email"]').exists()).toBe(true)
    expect(wrapper.find('input[type="password"]').exists()).toBe(true)
    expect(wrapper.find('input[name="password_confirmation"]').exists()).toBe(true)
    expect(wrapper.text()).toContain('Vytvorte si účet')
  })

  it('validates password mismatch', async () => {
    const wrapper = mount(RegisterView, { global: { stubs: { RouterLink: true } } })
    
    await wrapper.find('#name').setValue('John Doe')
    await wrapper.find('#email').setValue('john@example.com')
    await wrapper.find('#password').setValue('password123')
    await wrapper.find('#password_confirmation').setValue('mismatch')
    await wrapper.find('#agree-terms').setValue(true)
    
    await wrapper.find('form').trigger('submit')
    
    expect(mockToast.error).toHaveBeenCalledWith('Heslá sa nezhodujú')
    expect(mockRegister).not.toHaveBeenCalled()
  })

  it('calls register on valid submission', async () => {
    const wrapper = mount(RegisterView, { global: { stubs: { RouterLink: true } } })
    
    await wrapper.find('#name').setValue('John Doe')
    await wrapper.find('#email').setValue('john@example.com')
    await wrapper.find('#password').setValue('password123')
    await wrapper.find('#password_confirmation').setValue('password123')
    await wrapper.find('#agree-terms').setValue(true)
    
    await wrapper.find('form').trigger('submit')
    
    expect(mockRegister).toHaveBeenCalledWith(expect.objectContaining({
      name: 'John Doe',
      email: 'john@example.com',
      password: 'password123'
    }))
  })
})
