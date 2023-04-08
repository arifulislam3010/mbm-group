import { mount } from '@vue/test-utils'
import Loading from '@/components/loading'

test('setProps demo', async () => {
    const wrapper = mount(Loading)

    await wrapper.setProps({ loading: true })

    expect(wrapper.vm.loading).toBe(true)
})
