import { ref } from "vue";

export function useDarkMode() {
    let html = document.querySelector("html");
    let body = document.body;
    const theme = ref();

    function darkMode() {
        html.classList.add("dark");
        body.style.backgroundColor = "#141414";
        body.classList.add("text-white");
        theme.value = "dark";

        // save to localstorage
        localStorage.removeItem("theme");
        localStorage.setItem("theme", JSON.stringify(theme.value));
    }

    function lightMode() {
        html.classList.remove("dark");
        body.style.backgroundColor = "#eeebe6";
        body.classList.remove("text-white");
        theme.value = "light";

        // save to localstorage
        localStorage.removeItem("theme");
        localStorage.setItem("theme", JSON.stringify(theme.value));
    }

    return {
        darkMode,
        lightMode,
        theme,
    };
}
