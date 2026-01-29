import { ref, watch, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

export function useTheme() {
    const page = usePage();
    const currentTheme = ref('system');

    const updateDOM = (theme) => {
        const root = document.documentElement;
        if (theme === 'dark') {
            root.classList.add('dark');
        } else if (theme === 'light') {
            root.classList.remove('dark');
        } else if (theme === 'system') {
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                root.classList.add('dark');
            } else {
                root.classList.remove('dark');
            }
        }
    };

    const setTheme = (theme) => {
        currentTheme.value = theme;
        updateDOM(theme);

        // Update user profile if authenticated
        if (page.props.auth.user) {
            router.post(route('profile.theme'), {
                theme: theme,
                _method: 'PATCH',
            }, {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    // updated
                }
            });
        }
    };

    const initTheme = () => {
        // Load from user profile or local storage (if guest)
        if (page.props.auth.user?.profile?.theme) {
            currentTheme.value = page.props.auth.user.profile.theme;
        } else {
            // Default to system for guests or new users
            currentTheme.value = 'system';
        }

        updateDOM(currentTheme.value);

        // Listen for system changes if mode is system
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (currentTheme.value === 'system') {
                if (e.matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        });
    };

    return {
        currentTheme,
        setTheme,
        initTheme
    };
}
