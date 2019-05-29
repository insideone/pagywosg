<template>
    <div
        :class="[
            'messagebox',
            {'messagebox--loading': mode === 'loading'},
            {'messagebox--error': mode === 'error'},
        ]"
    >
        <slot name="icon">
            <i
                v-if="mode === 'loading'"
                class="icon-fa icon-fa--inline fas fa-hourglass-half"
            ></i>

            <i
                v-else-if="mode === 'error'"
                class="icon-fa icon-fa--inline fas fa-bug"
            ></i>

            <i
                v-else
                class="icon-fa icon-fa--inline fas fa-info-circle"
            ></i>
        </slot>

        <slot name="default">
            Loading.....
        </slot>
    </div>
</template>

<script>
    export default {
        name: "Messagebox",
        props: {
            mode: {
                type: String,
                default: ''
            }
        },
    }
</script>

<style lang="less">
    @import "../assets/mixins";

    .messagebox{
        border-left: 3px solid @color-blue;
        box-sizing: border-box;
        background: fade(@color-light-blue, 30%);
        padding: 10px 20px;
        color: @color-dark-blue;
        font-size: 16px;
        margin: 10px 0 16px;
        flex-grow: 1;

        &--loading{
            background: fade(@color-golden, 20%);
            border-left-color: @color-golden;
            color: @color-red;
        }

        &--error{
            background: fade(@color-red, 20%);
            border-left-color: @color-red;
            color: @color-red;
        }
    }

</style>