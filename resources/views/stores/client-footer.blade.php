   <footer class="bg-gray-800 text-white mt-16 bottom-0 left-0 right-0">
       <div class="container mx-auto px-4 py-8">
           <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
               <!-- Informações da Loja -->
               <div>
                   <h3 class="text-lg font-semibold mb-4">{{ $store->name }}</h3>
                   @if($store->slogan)
                   <p class="text-gray-300 mb-2">{{ $store->slogan }}</p>
                   @endif
                   @if($store->celphone)
                   <p class="text-gray-300">📞 {{ $store->celphone }}</p>
                   @endif
               </div>

               <!-- Links Rápidos -->
               <div>
                   <h3 class="text-lg font-semibold mb-4">Links Rápidos</h3>
                   <ul class="space-y-2 text-gray-300">
                       <li><a href="#" class="hover:text-white">Sobre Nós</a></li>
                       <li><a href="#" class="hover:text-white">Contato</a></li>
                       <li><a href="#" class="hover:text-white">Política de Privacidade</a></li>
                       <li><a href="#" class="hover:text-white">Termos de Uso</a></li>
                   </ul>
               </div>

               <!-- Redes Sociais -->
               <div>
                   <h3 class="text-lg font-semibold mb-4">Siga-nos</h3>
                   <div class="flex space-x-4">
                       @if($store->facebook)
                       <a href="{{ $store->facebook }}" target="_blank" class="text-gray-300 hover:text-white">
                           <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                               <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                           </svg>
                       </a>
                       @endif
                       @if($store->instagram)
                       <a href="{{ $store->instagram }}" target="_blank" class="text-gray-300 hover:text-white">
                           <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                               <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.297-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.807.875 1.297 2.026 1.297 3.323s-.49 2.448-1.297 3.323c-.875.807-2.026 1.297-3.323 1.297z" />
                           </svg>
                       </a>
                       @endif
                       @if($store->site)
                       <a href="{{ $store->site }}" target="_blank" class="text-gray-300 hover:text-white">
                           <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                           </svg>
                       </a>
                       @endif
                   </div>
               </div>
           </div>
       </div>
   </footer>