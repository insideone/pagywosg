<template>
    <div class="app">
        <header class="app__header">
            <div class="app__wrapper app__wrapper--flex-row">
                <router-link :to="{name: 'index'}" class="app__logo">
                    <img src="https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/57/57afeacd230e652328a173d8233a0a1bdb1fe510_full.jpg"
                         alt="pagywosg"
                         class="app__logo-img"
                    />
                </router-link>
                <nav class="nav app__main-mav">
                    <router-link :to="{name: 'events'}" class="nav__link">Events</router-link>
                    <router-link
                        v-if="eventCreateAllowed"
                        :to="{name: 'new_event'}"
                        class="nav__link"
                    >New Event</router-link>
                    <router-link
                        v-if="readingAnyUserAllowed"
                        :to="{name: 'users'}"
                        class="nav__link"
                    >Users</router-link>
                    <router-link
                        :to="{name: 'faq'}"
                        class="nav__link"
                    >FAQ</router-link>
                </nav>
                <steam-login-block class="nav__login-block" />
            </div>
        </header>
        <main class="app__main">
            <div class="app__wrapper app__main-area">
                <router-view :key="$route.path" />
            </div>
        </main>
        <footer class="app__footer">
            <div class="app__wrapper app__wrapper--flex-row">
                <div class="app__authors">{{year}}, Powered by Steam</div>
                <nav class="nav app__bottom-nav">
                    <a
                        href="https://www.steamgifts.com/discussions/search?q=play%20a%20game%20you%20won%20on%20Steamgifts"
                        class="nav__link" target="_blank"
                    >Steamgifts</a>
                    <a
                        href="https://steamcommunity.com/groups/playSGwins"
                        class="nav__link" target="_blank"
                    >Steam Group</a>
                    <router-link
                        :to="{name: 'help_formatting'}"
                        class="nav__link"
                    >Text Formatting</router-link>
                    <!-- TODO:
                        <a href="#" class="nav__link">FAQ</a>
                        <a href="#" class="nav__link">Help</a>
                        <a href="#" class="nav__link">Stats</a>
                        <a href="#" class="nav__link">Credits</a>
                        <a href="#" class="nav__link">Dark Theme</a>
                    -->
                </nav>
            </div>
        </footer>
    </div>
</template>

<script>
    import SteamLoginBlock from './components/SteamLoginBlock.vue';

    import {mapGetters} from 'vuex';

    export default {
        name: "App",
        components: {
            SteamLoginBlock
        },
        computed: {
            ...mapGetters({
                eventCreateAllowed: 'isCreatingEventAllowed',
                readingAnyUserAllowed: 'isReadingAnyUserAllowed'
            }),
            year: () => (new Date).getFullYear()
        }
    }
</script>

<style lang="less">
    @import './assets/mixins';
    @import '~normalize.css';
    @import "./assets/blocks/icon-fa";

    @import '~@fortawesome/fontawesome-free/less/fontawesome';
    @import '~@fortawesome/fontawesome-free/less/regular';
    @import '~@fortawesome/fontawesome-free/less/solid';
    @import '~@fortawesome/fontawesome-free/less/brands';


    @import "./assets/blocks/button";
    @import "./assets/blocks/nav";
    @import "./assets/blocks/text";

    html, body{
        height: 100%;
    }

    a{
        color: @color-pink;
        text-decoration: underline;
        transition: color 0.3s;

        &:hover{
            color: @color-red;
        }
    }

    input, textarea{
        font-family: sans-serif;
    }

    .app{
        min-height: 100%;
        display: flex;
        flex-direction: column;
        background: @color-bg;
        color: @color-dark;
        font-size: 15px;
        font-family: sans-serif;
        line-height: 1.3;

        &__header, &__footer{
            flex-shrink: 0;
        }

        &__main{
            flex-grow: 1;
        }

        &__wrapper{
            min-width: 940px;
            max-width: 1400px;
            margin: 0 auto;
            box-sizing: border-box;
            padding: 0 16px;

            &--flex-row{
                display: flex;
                justify-content: flex-start;
                align-items: stretch;
            }
        }

        &__header{
            border-bottom: 1px solid @color-pink;
        }

        &__logo{
            display: block;
            box-sizing: border-box;
            width: 52px;
            height: 52px;
            margin: 0 16px 0 0;
            transition: opacity 0.3s;

            &.router-link-exact-active{
                opacity: 0.75;
            }
        }

        &__logo-img{
            width: 100%;
            height: 100%;
        }

        &__main-mav{
            margin-right: auto;
        }

        &__bottom-nav{
            margin-left: auto;
        }

        &__footer{
            border-top: 1px solid @color-pink;
        }

        &__authors{
            display: flex;
            align-items: center;
            padding: 5px 0;
            color: @color-gray;
            font-size: 12px;
            font-style: italic;
        }

        &__main-area{
            padding-top: 20px;
            padding-bottom: 20px;
        }
    }
</style>