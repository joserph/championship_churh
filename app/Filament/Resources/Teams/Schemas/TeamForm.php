<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Team Information')
                    ->schema([
                        Select::make('championship_id')
                            ->relationship('championship', 'name')
                            ->required(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Eagles, Lions, Tigers...'),
                        
                        Textarea::make('description')
                            ->rows(3)
                            ->placeholder('Team description...'),
                        
                        Toggle::make('active')
                            ->default(true)
                            ->label('Participates in competition'),
                    ])->columns(1),
                
                // SECTION 2: Visual customization
                Section::make('Customization')
                    ->description('Colors and icon to identify the team')
                    ->schema([
                        ColorPicker::make('color')
                            ->label('Primary Color')
                            ->default('#3b82f6')
                            ->helperText('Color for buttons and highlighted elements'),
                        
                        ColorPicker::make('light_color')
                            ->label('Background Color')
                            ->default('#dbeafe')
                            ->helperText('Color for card backgrounds'),
                        
                        ColorPicker::make('dark_color')
                            ->label('Text Color')
                            ->default('#1e40af')
                            ->helperText('Color for text on light background'),
                        
                        Select::make('icon')
                            ->options([
                                'heroicon-o-flag' => '🚩 Flag',
                                'heroicon-o-fire' => '🔥 Fire',
                                'heroicon-o-shield-check' => '🛡️ Shield',
                                'heroicon-o-trophy' => '🏆 Trophy',
                                'heroicon-o-bolt' => '⚡ Lightning',
                                'heroicon-o-star' => '⭐ Star',
                                'heroicon-o-heart' => '❤️ Heart',
                                'heroicon-o-users' => '👥 Group',
                            ])
                            ->default('heroicon-o-flag')
                            ->helperText('Icon representing the team'),
                    ])->columns(2),
                

            ]);
    }
}
