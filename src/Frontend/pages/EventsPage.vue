<template>
    <div class="events-page">
        <h1 class="heading">List of All Events</h1>

        <div class="text">
            <p>On this page you can find the list of all events, managed through this website.</p>
        </div>

        <div class="events-page__top-line">

            <div class="events-page__filters filters">
                <!--TODO:-->
                <!--<div-->
                    <!--v-for="(filterItem, key) in filters"-->
                    <!--:key="'filter_item_'+key"-->
                    <!--:class="['filters__item', {'filters__item&#45;&#45;selected': filterItem.id === currentFilter}]"-->
                    <!--@click="changeFilter(filterItem.id)"-->
                <!--&gt;{{filterItem.name}}</div>-->
            </div>
            <div class="events-page__btns">
                <router-link v-if="eventCreateAllowed" :to="{name: 'new_event'}" class="button">
                    <i class="icon-fa icon-fa--inline fas fa-calendar-plus"></i>
                    Create New Event
                </router-link>
            </div>
        </div>

        <messagebox
            v-if="isLoading"
            mode="loading"
        />

        <div class="events-page__items" v-else>
            <event-item
                v-for="(eventItem, key) in events"
                :key="'event_'+key"
                :event="eventItem"
                :host="getUser(eventItem.host)"
            />
        </div>

    </div>
</template>

<script>
    import EventItem from "../components/EventItem.vue";
    import Messagebox from "../components/Messagebox.vue";

    import {mapActions, mapGetters} from 'vuex';

    export default {
        name: "EventsPage",
        components: {
            EventItem,
            Messagebox
        },
        data () {
            return {
                filters: [
                    {
                        name: 'All',
                        id: 1
                    },
                    {
                        name: 'Mine',
                        id: 2
                    },
                    {
                        name: 'Running',
                        id: 3,
                    },
                    {
                        name: 'Over',
                        id: 4
                    }
                ],
                currentFilter: 3,
                isLoading: true
            };
        },
        computed: {
          ...mapGetters({
              events: 'getShowedEvents',
              getUser: 'getUser',
              eventCreateAllowed: 'isCreatingEventAllowed'
          })
        },
        created () {
            this.doFetchEvents().finally(() => this.isLoading = false);
        },
        methods: {
            ...mapActions({
                doFetchEvents: 'fetchEvents'
            }),

            changeFilter (filterId) {
                this.currentFilter = filterId;
            }
        },
    }
</script>

<style lang="less">
    @import "../assets/mixins";

    @import "../assets/blocks/filters";

    .events-page{

        &__top-line{
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        &__filters{

        }

        &__items{

        }
    }

</style>