<template>
    <div>
        <nav class="navbar navbar-expand-sm navbar-dark bg-info mb-2">
            <div class="container">
                <!-- DEFAULT
                <a href="#" class="navbar-brand">My Page</a>
                -->
                <a 
                    class="navbar-brand" 
                    v-bind:href="routeHome"
                    v-if="strcmp(href, routeHome) !== 0">Home Page</a>
                <a 
                    class="navbar-brand" 
                    v-bind:href="routeMy"
                    v-if="strcmp(href, routeMy) !== 0">My Page</a>
                <a class="navbar-brand" v-bind:href="routeWelcome">Welcome Page</a>
            </div>
        </nav>
        <nav class="navbar navbar-light bg-light">
            <span class="navbar-brand mb-0 h1">{{ (strcmp(href, routeHome) !== 0) ? 'My Page' : 'Home Page' }}</span>
        </nav>
    </div>
</template>

<script>
    export default {
        props: {
            routeHome: {
                type: String
            },
            routeMy: {
                type: String
            },
            routeWelcome: {
                type: String
            }
        },
        data() {
            return {
                href: window.location.href
            };
        },
        created() {
            console.log(`this.routeHome: ${this.routeHome}`);
            console.log(`this.routeMy: ${this.routeMy}`);
            console.log(`this.routeWelcome: ${this.routeWelcome}`);
        },
        methods: {
            strcmp(a, b) {
                a = a.toString(), b = b.toString();
                for (var i = 0, n = Math.max(a.length, b.length); 
                    i < n && a.charAt(i) === b.charAt(i); 
                    ++i) {}
                if (i === n) 
                    return 0;
                return a.charAt(i) > b.charAt(i) ? -1 : 1;
            }
        }
    }
</script>