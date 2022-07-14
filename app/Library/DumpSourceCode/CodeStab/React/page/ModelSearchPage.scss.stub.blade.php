@import "resources/sass/{{ $domain }}/variables";
.{{ \Str::kebab(lcfirst($classBaseName)) }}-search-page {
    display: flex;
    flex-direction: column;
    gap: ${{ lcfirst($domain) }}-base-gap-size;
}
